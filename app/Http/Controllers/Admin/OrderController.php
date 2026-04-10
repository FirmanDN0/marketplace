<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'provider', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('provider', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'provider', 'service', 'package', 'payment', 'review', 'dispute']);
        return view('admin.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->withErrors(['error' => 'Cannot cancel this order.']);
        }

        $this->orderService->cancel($order, auth()->id(), $data['reason']);
        return back()->with('success', 'Order cancelled.');
    }
}
