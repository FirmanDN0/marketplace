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

        $data = $request->validate([
            'amount'         => "required|numeric|min:50000|max:{$profile->balance}",
            'method'         => 'required|in:bank_transfer,paypal,gopay,dana',
            'account_name'   => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
        ]);

        WithdrawRequest::create([
            'provider_id'     => $user->id,   // reuse provider_id column for any user
            'amount'          => $data['amount'],
            'method'          => $data['method'],
            'account_details' => [
                'name'   => $data['account_name'],
                'number' => $data['account_number'],
            ],
            'status' => 'pending',
        ]);

        return redirect()->route('wallet.index')
            ->with('success', 'Withdraw request submitted. Admin will process it within 1-3 business days.');
    }
}
