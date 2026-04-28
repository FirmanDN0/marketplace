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
            'action'     => 'required|in:refund_customer,release_to_provider',
        ]);

        $dispute->update([
            'resolution'  => $data['resolution'],
            'status'      => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        if ($dispute->order && !in_array($dispute->order->status, ['cancelled', 'completed'])) {
            if ($data['action'] === 'refund_customer') {
                (new OrderService())->cancel($dispute->order, auth()->id(), 'Dispute resolved in favor of customer: ' . $data['resolution']);
            } elseif ($data['action'] === 'release_to_provider') {
                // Admin sides with provider, force completion
                (new OrderService())->complete($dispute->order);
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

        return back()->with('success', 'Dispute resolved successfully.');
    }
}
