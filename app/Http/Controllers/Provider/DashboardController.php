<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $provider = auth()->user();

        $stats = [
            'total_services'   => Service::where('provider_id', $provider->id)->count(),
            'active_services'  => Service::where('provider_id', $provider->id)->where('status', 'active')->count(),
            'total_orders'     => Order::where('provider_id', $provider->id)->count(),
            'active_orders'    => Order::where('provider_id', $provider->id)->whereIn('status', ['paid', 'in_progress', 'delivered'])->count(),
            'completed_orders' => Order::where('provider_id', $provider->id)->where('status', 'completed')->count(),
            'avg_rating'       => Review::where('provider_id', $provider->id)->avg('rating'),
            'balance'          => optional($provider->profile)->balance ?? 0,
            'total_earned'     => optional($provider->profile)->total_earned ?? 0,
        ];

        $recentOrders = Order::where('provider_id', $provider->id)
            ->with(['customer', 'service'])
            ->latest()
            ->limit(8)
            ->get();

        return view('provider.dashboard', compact('stats', 'recentOrders'));
    }
}
