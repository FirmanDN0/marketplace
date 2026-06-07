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
            return back()->withErrors(['error' => 'Hanya permintaan pending yang dapat disetujui.']);
        }

        $withdrawRequest->update([
            'status'       => 'processed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Update the transaction description if needed, or just let it be.
        $transaction = Transaction::where('reference_id', 'WD-' . $withdrawRequest->id)->first();
        if ($transaction) {
            $transaction->update(['description' => 'Penarikan dana berhasil (Selesai)']);
        }

        \App\Services\NotificationService::send(
            $withdrawRequest->provider_id,
            'withdraw_processed',
            'Penarikan Dana Berhasil',
            "Penarikan dana sebesar Rp " . number_format($withdrawRequest->amount, 0, ',', '.') . " telah berhasil ditransfer ke rekening Anda.",
            ['withdraw_id' => $withdrawRequest->id]
        );

        return back()->with('success', 'Penarikan dana disetujui dan selesai.');
    }

    public function reject(Request $request, WithdrawRequest $withdrawRequest)
    {
        if ($withdrawRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya permintaan pending yang dapat ditolak.']);
        }

        $data = $request->validate(['notes' => 'required|string|max:500']);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $withdrawRequest->update([
                'status'       => 'rejected',
                'notes'        => $data['notes'],
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Restore balance
            $profile = UserProfile::where('user_id', $withdrawRequest->provider_id)->first();
            $balanceBefore = $profile->balance;
            $profile->increment('balance', $withdrawRequest->amount);

            // Record refund transaction
            Transaction::create([
                'user_id'        => $withdrawRequest->provider_id,
                'type'           => 'refund',
                'amount'         => $withdrawRequest->amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $withdrawRequest->amount,
                'description'    => 'Pengembalian dana (Penarikan ditolak): ' . $data['notes'],
                'reference_id'   => 'WDRF-' . $withdrawRequest->id,
            ]);

            \App\Services\NotificationService::send(
                $withdrawRequest->provider_id,
                'withdraw_rejected',
                'Penarikan Dana Ditolak',
                "Permintaan penarikan dana Anda ditolak: {$data['notes']}. Saldo telah dikembalikan.",
                ['withdraw_id' => $withdrawRequest->id]
            );

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Penarikan dana ditolak dan saldo dikembalikan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menolak penarikan.']);
        }
    }
}
