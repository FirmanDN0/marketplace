@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
            <p class="text-gray-500 text-sm mt-1">Track and manage your purchases</p>
        </div>
        <a href="{{ route('services.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2 self-start sm:self-auto">
            <i class="fas fa-plus"></i> Place New Order
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="status" class="rounded-xl border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    @foreach(['pending_payment','paid','in_progress','delivered','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
                @if(request('status'))
                    <a href="{{ route('customer.orders.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition">Clear</a>
                @endif
            </form>
        </div>
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($orders as $order)
            <a href="{{ route('customer.orders.show', $order->id) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-blue-600">{{ $order->order_number }}</span>
                    @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-cyan-100 text-cyan-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                    <span class="{{ $sc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                </div>
                <div class="text-sm text-gray-700 font-medium truncate mb-1">{{ Str::limit(optional($order->service)->title,50) }}</div>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                    <span class="font-semibold text-gray-900 text-sm">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                </div>
            </a>
            @empty
            <div class="py-12 text-center text-gray-400">No orders yet.</div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium">Order #</th>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium">Service</th>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium hidden md:table-cell">Provider</th>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium">Status</th>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium">Price</th>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium hidden md:table-cell">Date</th>
                        <th class="px-4 lg:px-5 py-3 text-left font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 lg:px-5 py-3">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700 font-medium">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-4 lg:px-5 py-3 text-gray-700">{{ Str::limit(optional($order->service)->title,40) }}</td>
                    <td class="px-4 lg:px-5 py-3 text-gray-600 hidden md:table-cell">{{ optional($order->provider)->name }}</td>
                    <td class="px-4 lg:px-5 py-3">
                        @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-cyan-100 text-cyan-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                        <span class="{{ $sc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </td>
                    <td class="px-4 lg:px-5 py-3 font-semibold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-5 py-3 text-gray-500 hidden md:table-cell">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 lg:px-5 py-3">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700 font-medium text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-12 text-center text-gray-400">No orders yet.</td></tr>
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
