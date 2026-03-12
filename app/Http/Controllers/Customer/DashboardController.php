<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = auth()->user();

        $stats = [
            'total_orders'     => Order::where('customer_id', $customer->id)->count(),
            'active_orders'    => Order::where('customer_id', $customer->id)
                                       ->whereIn('status', ['paid', 'in_progress', 'delivered'])->count(),
            'completed_orders' => Order::where('customer_id', $customer->id)->where('status', 'completed')->count(),
            'total_spent'      => optional($customer->profile)->total_spent ?? 0,
            'balance'          => optional($customer->profile)->balance ?? 0,
        ];

        $recentOrders = Order::where('customer_id', $customer->id)
            ->with(['service', 'provider', 'package'])
            ->latest()
            ->limit(8)
            ->get();

        return view('customer.dashboard', compact('stats', 'recentOrders'));
    }
}
