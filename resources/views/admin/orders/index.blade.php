@extends('layouts.app')
@section('title', 'Admin: Orders')
@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">All Orders</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Filter --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, customer, provider…"
                       class="rounded-xl border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-56">
                <select name="status" class="rounded-xl border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    @foreach(['pending_payment','paid','in_progress','delivered','completed','cancelled','disputed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" title="Dari tanggal">
                <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" title="Sampai tanggal">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
                @if(request()->hasAny(['search','status','from','to']))
                    <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Clear</a>
                @endif
            </form>
        </div>
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($orders as $order)
            <a href="{{ route('admin.orders.show', $order->id) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-blue-600">{{ $order->order_number }}</span>
                    @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ str_replace('_',' ',$order->status) }}</span>
                </div>
                <div class="text-sm text-gray-700 truncate mb-1">{{ Str::limit(optional($order->service)->title, 45) }}</div>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>{{ optional($order->customer)->name }} &rarr; {{ optional($order->provider)->name }}</span>
                    <span class="font-semibold text-gray-900 text-sm">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                </div>
            </a>
            @empty
            <div class="px-4 py-8 text-center text-gray-400">No orders found.</div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600">Order #</th>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Customer</th>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Provider</th>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600 hidden lg:table-cell">Service</th>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600">Price</th>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-4 lg:px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Date</th>
                        <th class="px-4 lg:px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 lg:px-5 py-3 font-medium text-blue-600">
                        <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-4 lg:px-5 py-3 text-gray-700 hidden md:table-cell">{{ optional($order->customer)->name }}</td>
                    <td class="px-4 lg:px-5 py-3 text-gray-700 hidden md:table-cell">{{ optional($order->provider)->name }}</td>
                    <td class="px-4 lg:px-5 py-3 text-gray-700 hidden lg:table-cell">{{ Str::limit(optional($order->service)->title, 40) }}</td>
                    <td class="px-4 lg:px-5 py-3 font-semibold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-5 py-3">
                        @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ str_replace('_',' ',$order->status) }}</span>
                    </td>
                    <td class="px-4 lg:px-5 py-3 text-gray-500 hidden md:table-cell">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 lg:px-5 py-3"><a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">No orders found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
