@extends('layouts.app')
@section('title', 'Platform Reports')
@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Platform Reports</h1>
    </div>

    {{-- Revenue --}}
    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2"><i class="fas fa-wallet text-blue-500"></i> Revenue</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3"><i class="fas fa-calendar-day"></i></div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue['today'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 mt-1">Today</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3"><i class="fas fa-calendar-alt"></i></div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue['this_month'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 mt-1">This Month</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center mb-3"><i class="fas fa-credit-card"></i></div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue['total'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 mt-1">All Time</div>
        </div>
    </div>

    {{-- Orders --}}
    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2"><i class="fas fa-box text-orange-500"></i> Orders</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-gray-900">{{ $orders['today'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Today</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-gray-900">{{ $orders['this_month'] }}</div>
            <div class="text-sm text-gray-500 mt-1">This Month</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $orders['completed'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Completed</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="text-3xl font-bold text-red-500">{{ $orders['cancelled'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Cancelled</div>
        </div>
    </div>

    {{-- Users & Withdrawals --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-users text-blue-500"></i> Users</h3></div>
            <div class="p-5 space-y-3">
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-gray-600">Total Providers</span>
                    <span class="text-sm font-bold text-gray-900">{{ $users['total_providers'] }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-gray-100">
                    <span class="text-sm text-gray-600">Total Customers</span>
                    <span class="text-sm font-bold text-gray-900">{{ $users['total_customers'] }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-t border-gray-100">
                    <span class="text-sm text-gray-600">New Today</span>
                    <span class="text-sm font-bold text-blue-600">{{ $users['new_today'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-university text-orange-500"></i> Pending Withdrawals</h3></div>
            <div class="p-5 text-center">
                <div class="text-3xl font-bold text-orange-600 mb-2">Rp {{ number_format($pendingWithdrawals, 0, ',', '.') }}</div>
                <p class="text-sm text-gray-500 mb-4">Total amount awaiting approval</p>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="inline-flex items-center gap-2 bg-orange-50 text-orange-600 hover:bg-orange-100 px-4 py-2 rounded-xl text-sm font-medium transition">
                    <i class="fas fa-arrow-right text-xs"></i> View Pending Withdrawals
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
