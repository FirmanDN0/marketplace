<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $revenue = [
            'today'      => Transaction::where('type', 'fee')->whereDate('created_at', today())->sum('amount'),
            'this_month' => Transaction::where('type', 'fee')->whereMonth('created_at', now()->month)->sum('amount'),
            'total'      => Transaction::where('type', 'fee')->sum('amount'),
        ];

        $orders = [
            'today'      => Order::whereDate('created_at', today())->count(),
            'this_month' => Order::whereMonth('created_at', now()->month)->count(),
            'total'      => Order::count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        $users = [
            'total_providers' => User::where('role', 'provider')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'new_today'       => User::whereDate('created_at', today())->count(),
        ];

        $pendingWithdrawals = WithdrawRequest::where('status', 'pending')->sum('amount');

        return view('admin.reports.index', compact('revenue', 'orders', 'users', 'pendingWithdrawals'));
    }
}
