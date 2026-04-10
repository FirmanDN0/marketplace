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

        $data = $request->validate([
            'amount'          => "required|numeric|min:50000|max:{$profile->balance}",
            'method'          => 'required|in:bank_transfer,paypal,gopay,dana',
            'account_name'    => 'required|string|max:100',
            'account_number'  => 'required|string|max:100',
        ]);

        WithdrawRequest::create([
            'provider_id'     => $provider->id,
            'amount'          => $data['amount'],
            'method'          => $data['method'],
            'account_details' => [
                'name'   => $data['account_name'],
                'number' => $data['account_number'],
            ],
            'status' => 'pending',
        ]);

        return redirect()->route('provider.withdraw.index')->with('success', 'Withdrawal request submitted.');
    }
}
