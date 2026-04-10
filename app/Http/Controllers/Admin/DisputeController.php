<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispute::with(['order', 'opener', 'resolver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disputes = $query->latest()->paginate(20)->withQueryString();
        return view('admin.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load(['order.customer', 'order.provider', 'opener', 'resolver']);
        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $data = $request->validate([
            'resolution' => 'required|string|max:1000',
            'status'     => 'required|in:resolved,closed',
            'refund'     => 'nullable|in:1',
        ]);

        $dispute->update([
            'resolution'  => $data['resolution'],
            'status'      => $data['status'],
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        // Process refund if requested
        if (!empty($data['refund']) && $dispute->order) {
            $order   = $dispute->order;
            $payment = $order->payment;

            if ($payment && $payment->status === 'success' && !in_array($order->status, ['cancelled', 'completed'])) {
                (new OrderService())->cancel($order, auth()->id(), 'Dibatalkan oleh admin: ' . $data['resolution']);
            }
        }

        // Notify both parties
        \App\Services\NotificationService::send(
            $dispute->order->customer_id,
            'dispute_resolved',
            'Dispute Resolved',
            "The dispute for order #{$dispute->order->order_number} has been resolved.",
            ['dispute_id' => $dispute->id]
        );

        \App\Services\NotificationService::send(
            $dispute->order->provider_id,
            'dispute_resolved',
            'Dispute Resolved',
            "The dispute for order #{$dispute->order->order_number} has been resolved.",
            ['dispute_id' => $dispute->id]
        );

        return back()->with('success', 'Dispute resolved.');
    }
}
