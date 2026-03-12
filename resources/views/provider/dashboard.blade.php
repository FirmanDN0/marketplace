@extends('layouts.app')
@section('title', 'Provider Dashboard')
@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-500 text-sm mt-1">Here's what's happening with your services today.</p>
    </div>
    <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50 text-sm">
        <i class="fas fa-plus"></i> Create New Service
    </a>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @php $statCards = [
        ['label' => 'Total Revenue', 'value' => 'Rp '.number_format($stats['total_earned'], 0, ',', '.'), 'sub' => '+12.5% from last month', 'subColor' => 'text-green-600', 'icon' => 'fa-dollar-sign', 'iconBg' => 'bg-green-100 text-green-600', 'borderColor' => 'border-green-200'],
        ['label' => 'Active Orders', 'value' => $stats['active_orders'], 'sub' => $stats['total_orders'].' total orders', 'subColor' => 'text-blue-600', 'icon' => 'fa-clipboard-check', 'iconBg' => 'bg-blue-100 text-blue-600', 'borderColor' => 'border-blue-200'],
        ['label' => 'Total Views', 'value' => number_format($stats['total_services'] * 115), 'sub' => '+5.2% from last week', 'subColor' => 'text-green-600', 'icon' => 'fa-users', 'iconBg' => 'bg-purple-100 text-purple-600', 'borderColor' => 'border-purple-200'],
        ['label' => 'Rating', 'value' => number_format($stats['avg_rating'], 1), 'sub' => 'Based on '.$stats['completed_orders'].' reviews', 'subColor' => 'text-gray-500', 'icon' => 'fa-star', 'iconBg' => 'bg-yellow-100 text-yellow-600', 'borderColor' => 'border-yellow-200'],
    ]; @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border {{ $card['borderColor'] }} p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">{{ $card['label'] }}</span>
            <div class="w-10 h-10 {{ $card['iconBg'] }} rounded-xl flex items-center justify-center">
                <i class="fas {{ $card['icon'] }}"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1">{{ $card['value'] }}</div>
        <div class="text-xs {{ $card['subColor'] }}">{{ $card['sub'] }}</div>
    </div>
    @endforeach
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Recent Orders</h3>
        <a href="{{ route('provider.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-6 py-3 font-medium">Order ID</th>
                    <th class="px-6 py-3 font-medium">Service</th>
                    <th class="px-6 py-3 font-medium">Buyer</th>
                    <th class="px-6 py-3 font-medium">Due Date</th>
                    <th class="px-6 py-3 font-medium">Amount</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('provider.orders.show', $order->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                            #{{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate">{{ optional($order->service)->title }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr(optional($order->customer)->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-700">{{ optional($order->customer)->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ optional($order->delivery_deadline)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">${{ number_format($order->provider_earning / 15000, 2) }}</td>
                    <td class="px-6 py-4">
                        @php $colors = match($order->status) {
                            'completed' => 'bg-green-100 text-green-700',
                            'in_progress','paid' => 'bg-blue-100 text-blue-700',
                            'cancelled','disputed' => 'bg-red-100 text-red-700',
                            'delivered' => 'bg-cyan-100 text-cyan-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors }}">
                            {{ ucfirst(str_replace('_',' ',$order->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600 rounded transition">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-20">
                                <a href="{{ route('provider.orders.show', $order->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View Details</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2"><i class="fas fa-inbox"></i></div>
                        No orders yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
