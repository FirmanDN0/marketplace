<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth()->user();

        $stats = [
            'total_users'     => User::where('role', '!=', 'admin')->count(),
            'total_providers' => User::where('role', 'provider')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_services'  => Service::count(),
            'active_services' => Service::where('status', 'active')->count(),
            'total_orders'    => Order::count(),
            'pending_orders'  => Order::where('status', 'pending_payment')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_revenue'   => Transaction::where('type', 'fee')->sum('amount'),
            'balance'         => optional($admin->profile)->balance ?? 0,
        ];

        $recentOrders = Order::with(['customer', 'provider', 'service'])
            ->latest()
            ->limit(10)
            ->get();

        $recentUsers = User::latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentUsers'));
    }
}
