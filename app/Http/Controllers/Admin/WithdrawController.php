<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserProfile;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawRequest::with('provider');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(20)->withQueryString();
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve(WithdrawRequest $withdrawRequest)
    {
        if ($withdrawRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be approved.']);
        }

        $profile = UserProfile::where('user_id', $withdrawRequest->provider_id)->first();

        if (!$profile || $profile->balance < $withdrawRequest->amount) {
            return back()->withErrors(['error' => 'Insufficient provider balance.']);
        }

        $balanceBefore = $profile->balance;
        $profile->decrement('balance', $withdrawRequest->amount);

        Transaction::create([
            'user_id'        => $withdrawRequest->provider_id,
            'type'           => 'withdrawal',
            'amount'         => -$withdrawRequest->amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceBefore - $withdrawRequest->amount,
            'description'    => 'Withdrawal approved',
            'reference_id'   => 'WD-' . $withdrawRequest->id,
        ]);

        $withdrawRequest->update([
            'status'       => 'processed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        \App\Services\NotificationService::send(
            $withdrawRequest->provider_id,
            'withdraw_processed',
            'Withdrawal Processed',
            "Your withdrawal of {$withdrawRequest->amount} has been processed.",
            ['withdraw_id' => $withdrawRequest->id]
        );

        return back()->with('success', 'Withdrawal approved and processed.');
    }

    public function reject(Request $request, WithdrawRequest $withdrawRequest)
    {
        $data = $request->validate(['notes' => 'required|string|max:500']);

        $withdrawRequest->update([
            'status'       => 'rejected',
            'notes'        => $data['notes'],
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        \App\Services\NotificationService::send(
            $withdrawRequest->provider_id,
            'withdraw_rejected',
            'Withdrawal Rejected',
            "Your withdrawal request has been rejected: {$data['notes']}",
            ['withdraw_id' => $withdrawRequest->id]
        );

        return back()->with('success', 'Withdrawal rejected.');
    }
}
