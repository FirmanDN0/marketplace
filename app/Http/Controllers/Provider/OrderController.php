<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::where('provider_id', auth()->id())
            ->with(['customer', 'service', 'package']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        return view('provider.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->provider_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['customer.profile', 'service', 'package', 'payment', 'review']);
        return view('provider.orders.show', compact('order'));
    }

    public function startWork(Order $order)
    {
        if ($order->provider_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isPaid()) {
            return back()->withErrors(['error' => 'Order is not paid yet.']);
        }

        $order->update(['status' => 'in_progress']);

        \App\Services\NotificationService::send(
            $order->customer_id,
            'order_in_progress',
            'Work Started',
            "Provider has started working on order #{$order->order_number}.",
            ['order_id' => $order->id],
            route('customer.orders.show', $order->id)
        );

        return back()->with('success', 'Order moved to In Progress.');
    }

    public function deliver(Request $request, Order $order)
    {
        if ($order->provider_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isInProgress()) {
            return back()->withErrors(['error' => 'Order must be in progress to deliver.']);
        }

        $data = $request->validate([
            'delivery_message' => 'required|string|min:20',
            'delivery_file'    => 'nullable|file|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('delivery_file')) {
            $filePath = $request->file('delivery_file')->store('deliveries', 'public');
        }

        $this->orderService->deliver($order, $data['delivery_message'], $filePath);

        return back()->with('success', 'Delivery submitted successfully.');
    }
}
