@extends('layouts.app')
@section('title', 'Provider Orders')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-600">
                <option value="">All Status</option>
                @foreach(['pending_payment','paid','in_progress','delivered','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">Filter</button>
            @if(request('status'))
                <a href="{{ route('provider.orders.index') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-red-500 transition">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-6 py-3 font-medium">Order #</th>
                    <th class="px-6 py-3 font-medium">Customer</th>
                    <th class="px-6 py-3 font-medium">Service</th>
                    <th class="px-6 py-3 font-medium">Package</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Earning</th>
                    <th class="px-6 py-3 font-medium">Deadline</th>
                    <th class="px-6 py-3 font-medium w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('provider.orders.show', $order->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">#{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr(optional($order->customer)->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-700">{{ optional($order->customer)->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate">{{ optional($order->service)->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ optional($order->package)->name }}</td>
                    <td class="px-6 py-4">
                        @php $colors = match($order->status) {
                            'completed' => 'bg-green-100 text-green-700',
                            'in_progress','paid' => 'bg-blue-100 text-blue-700',
                            'cancelled','disputed' => 'bg-red-100 text-red-700',
                            'delivered' => 'bg-cyan-100 text-cyan-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ optional($order->delivery_deadline)->format('M d') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('provider.orders.show', $order->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2"><i class="fas fa-inbox"></i></div>
                        No orders yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>

@endsection
