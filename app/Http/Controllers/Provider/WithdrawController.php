<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $provider = auth()->user();
        $profile  = $provider->profile ?? UserProfile::firstOrCreate(['user_id' => $provider->id]);

        $withdrawals = WithdrawRequest::where('provider_id', $provider->id)
            ->latest()
            ->paginate(15);

        return view('provider.withdraw.index', compact('withdrawals', 'profile'));
    }

    public function create()
    {
        $provider = auth()->user();
        $profile  = $provider->profile ?? UserProfile::firstOrCreate(['user_id' => $provider->id]);

        if ($profile->balance <= 0) {
            return redirect()->route('provider.withdraw.index')
                ->withErrors(['error' => 'Insufficient balance for withdrawal.']);
        }

        return view('provider.withdraw.create', compact('profile'));
    }

    public function store(Request $request)
    {
        $provider = auth()->user();
        $profile  = UserProfile::where('user_id', $provider->id)->firstOrFail();

        // Prevent multiple pending requests
        if (WithdrawRequest::where('provider_id', $provider->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['error' => 'Anda sudah memiliki permintaan penarikan yang sedang diproses.']);
        }

        $data = $request->validate([
            'amount'          => "required|numeric|min:50000|max:{$profile->balance}",
            'method'          => 'required|in:bank_transfer,paypal,gopay,dana',
            'account_name'    => 'required|string|max:100',
            'account_number'  => 'required|string|max:100',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Lock and deduct balance immediately
            $balanceBefore = $profile->balance;
            $profile->decrement('balance', $data['amount']);

            $withdrawRequest = WithdrawRequest::create([
                'provider_id'     => $provider->id,
                'amount'          => $data['amount'],
                'method'          => $data['method'],
                'account_details' => [
                    'name'   => $data['account_name'],
                    'number' => $data['account_number'],
                ],
                'status' => 'pending',
            ]);

            // Record transaction
            \App\Models\Transaction::create([
                'user_id'        => $provider->id,
                'type'           => 'withdrawal',
                'amount'         => -$data['amount'],
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $data['amount'],
                'description'    => 'Penarikan dana diproses (Pending)',
                'reference_id'   => 'WD-' . $withdrawRequest->id,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('provider.withdraw.index')->with('success', 'Permintaan penarikan dana berhasil dikirim.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses penarikan. Silakan coba lagi.']);
        }
    }
}
