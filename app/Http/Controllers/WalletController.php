<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TopUpController;
use App\Models\TopUp;
use App\Models\Transaction;
use App\Models\UserProfile;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Wallet overview: balance, top-up history, transaction history.
     */
    public function index()
    {
        $user    = auth()->user();
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        // Auto-sync pending top-ups with Midtrans before displaying
        TopUpController::syncPendingTopUps($user->id);
        $profile->refresh();

        $topUps       = TopUp::where('user_id', $user->id)->latest()->take(10)->get();
        $transactions = Transaction::where('user_id', $user->id)->latest()->take(15)->get();
        $withdrawals  = WithdrawRequest::where('provider_id', $user->id)->latest()->take(10)->get();

        return view('wallet.index', compact('profile', 'topUps', 'transactions', 'withdrawals'));
    }

    /**
     * Show withdraw request form.
     */
    public function withdrawCreate()
    {
        $user    = auth()->user();
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        return view('wallet.withdraw', compact('profile'));
    }

    /**
     * Store a new withdraw request (available for all user roles).
     */
    public function withdrawStore(Request $request)
    {
        $user    = auth()->user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        // Prevent multiple pending requests
        if (WithdrawRequest::where('provider_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['error' => 'Anda sudah memiliki permintaan penarikan yang sedang diproses.']);
        }

        $data = $request->validate([
            'amount'         => "required|numeric|min:50000|max:{$profile->balance}",
            'method'         => 'required|in:bank_transfer,paypal,gopay,dana',
            'account_name'   => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Lock and deduct balance immediately
            $balanceBefore = $profile->balance;
            $profile->decrement('balance', $data['amount']);

            $withdrawRequest = WithdrawRequest::create([
                'provider_id'     => $user->id,   // reuse provider_id column for any user
                'amount'          => $data['amount'],
                'method'          => $data['method'],
                'account_details' => [
                    'name'   => $data['account_name'],
                    'number' => $data['account_number'],
                ],
                'status' => 'pending',
            ]);

            // Record transaction
            Transaction::create([
                'user_id'        => $user->id,
                'type'           => 'withdrawal',
                'amount'         => -$data['amount'],
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $data['amount'],
                'description'    => 'Penarikan dana diproses (Pending)',
                'reference_id'   => 'WD-' . $withdrawRequest->id,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('wallet.index')
                ->with('success', 'Permintaan penarikan dana berhasil dikirim.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses penarikan. Silakan coba lagi.']);
        }
    }
}
