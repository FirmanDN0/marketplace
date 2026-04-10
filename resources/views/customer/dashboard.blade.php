@extends('layouts.app')
@section('title', 'Customer Dashboard')
@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-500 text-sm mt-1">Manage your orders and discover new services.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition text-sm">
            <i class="fas fa-wallet text-gray-400"></i> Wallet
        </a>
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50 text-sm">
            Browse Services
        </a>
    </div>
</div>

{{-- Wallet Banner --}}
<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-4 sm:p-6 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-white">
        <div class="text-sm opacity-80 mb-1">My Wallet Balance</div>
        <div class="text-3xl font-bold">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('wallet.topup.create') }}" class="px-5 py-2.5 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition text-sm shadow">+ Top Up</a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-8">
    @php $statCards = [
        ['label' => 'Total Orders', 'value' => $stats['total_orders'], 'icon' => 'fa-box', 'iconBg' => 'bg-blue-100 text-blue-600', 'border' => 'border-blue-200'],
        ['label' => 'Active Orders', 'value' => $stats['active_orders'], 'icon' => 'fa-hourglass-half', 'iconBg' => 'bg-yellow-100 text-yellow-600', 'border' => 'border-yellow-200'],
        ['label' => 'Completed', 'value' => $stats['completed_orders'], 'icon' => 'fa-check-circle', 'iconBg' => 'bg-green-100 text-green-600', 'border' => 'border-green-200'],
        ['label' => 'Total Spent', 'value' => 'Rp '.number_format($stats['total_spent'], 0, ',', '.'), 'icon' => 'fa-money-bill-wave', 'iconBg' => 'bg-purple-100 text-purple-600', 'border' => 'border-purple-200'],
    ]; @endphp
    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border {{ $card['border'] }} p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-500">{{ $card['label'] }}</span>
            <div class="w-10 h-10 {{ $card['iconBg'] }} rounded-xl flex items-center justify-center">
                <i class="fas {{ $card['icon'] }}"></i>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</div>
    </div>
    @endforeach
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Recent Orders</h3>
        <a href="{{ route('customer.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition">View All</a>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @forelse($recentOrders as $order)
        <a href="{{ route('customer.orders.show', $order->id) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-blue-600">{{ $order->order_number }}</span>
                @php $colors = match($order->status) {
                    'completed' => 'bg-green-100 text-green-700',
                    'in_progress','paid' => 'bg-blue-100 text-blue-700',
                    'cancelled','disputed' => 'bg-red-100 text-red-700',
                    'delivered' => 'bg-cyan-100 text-cyan-700',
                    default => 'bg-yellow-100 text-yellow-700'
                }; @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
            </div>
            <p class="text-sm text-gray-700 truncate mb-1">{{ optional($order->service)->title }}</p>
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ optional($order->provider)->name }}</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
            </div>
        </a>
        @empty
        <div class="px-4 py-12 text-center">
            <div class="text-4xl text-gray-200 mb-3"><i class="fas fa-inbox"></i></div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">No orders yet</h3>
            <p class="text-gray-500 text-sm mb-4">Explore talented providers and find your perfect service.</p>
            <a href="{{ route('services.index') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">Browse Services</a>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-4 lg:px-6 py-3 font-medium">Order #</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Service</th>
                    <th class="px-4 lg:px-6 py-3 font-medium hidden md:table-cell">Provider</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Status</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Price</th>
                    <th class="px-4 lg:px-6 py-3 font-medium hidden md:table-cell">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 lg:px-6 py-4">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate">{{ optional($order->service)->title }}</td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-600 hidden md:table-cell">{{ optional($order->provider)->name }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        @php $colors = match($order->status) {
                            'completed' => 'bg-green-100 text-green-700',
                            'in_progress','paid' => 'bg-blue-100 text-blue-700',
                            'cancelled','disputed' => 'bg-red-100 text-red-700',
                            'delivered' => 'bg-cyan-100 text-cyan-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-500 hidden md:table-cell">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="text-4xl text-gray-200 mb-3"><i class="fas fa-inbox"></i></div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">No orders yet</h3>
                        <p class="text-gray-500 text-sm mb-4">Explore talented providers and find your perfect service.</p>
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">Browse Services</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
