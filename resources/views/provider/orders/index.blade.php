@extends('layouts.app')
@section('title', 'Pesanan Masuk')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
            <i class="fas fa-clipboard-check text-lg"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pesanan Masuk</h1>
            <p class="text-gray-500 text-sm font-medium mt-0.5">Kelola pesanan dari pelanggan Anda</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl border border-gray-100/80 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100/80">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200/80 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-600 font-medium">
                <option value="">Semua Status</option>
                @foreach(['pending_payment','paid','in_progress','delivered','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-bold hover:from-blue-500 hover:to-indigo-500 transition-all shadow-md shadow-blue-500/10">Filter</button>
            @if(request('status'))
                <a href="{{ route('provider.orders.index') }}" class="text-sm text-gray-400 hover:text-red-500 font-semibold transition-colors"><i class="fas fa-times mr-1"></i>Hapus</a>
            @endif
        </form>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @forelse($orders as $order)
        <a href="{{ route('provider.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-blue-50/30 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-blue-600">#{{ $order->order_number }}</span>
                @php $colors = match($order->status) {
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'in_progress','paid' => 'bg-blue-100 text-blue-700',
                    'cancelled','disputed' => 'bg-red-100 text-red-700',
                    'delivered' => 'bg-cyan-100 text-cyan-700',
                    default => 'bg-yellow-100 text-yellow-700'
                }; @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $colors }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
            </div>
            <div class="text-sm text-gray-700 font-medium truncate mb-1">{{ optional($order->service)->title }}</div>
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span class="font-medium">{{ optional($order->customer)->name }}</span>
                <span class="font-bold text-gray-800 text-sm">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</span>
            </div>
        </a>
        @empty
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-2xl text-blue-300"></i></div>
            <h3 class="font-bold text-gray-800 mb-1">Belum ada pesanan</h3>
            <p class="text-gray-400 text-sm font-medium">Buat layanan baru dan mulai terima pesanan.</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3.5 font-semibold">ID Pesanan</th>
                    <th class="px-6 py-3.5 font-semibold">Pembeli</th>
                    <th class="px-6 py-3.5 font-semibold hidden md:table-cell">Layanan</th>
                    <th class="px-6 py-3.5 font-semibold">Status</th>
                    <th class="px-6 py-3.5 font-semibold">Pendapatan</th>
                    <th class="px-6 py-3.5 font-semibold hidden md:table-cell">Tenggat</th>
                    <th class="px-6 py-3.5 font-semibold w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('provider.orders.show', $order->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">#{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                {{ strtoupper(substr(optional($order->customer)->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-700 font-medium">{{ optional($order->customer)->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate hidden md:table-cell font-medium">{{ optional($order->service)->title }}</td>
                    <td class="px-6 py-4">
                        @php $colors = match($order->status) {
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'in_progress','paid' => 'bg-blue-100 text-blue-700',
                            'cancelled','disputed' => 'bg-red-100 text-red-700',
                            'delivered' => 'bg-cyan-100 text-cyan-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $colors }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm hidden md:table-cell">
                        @if($order->delivery_deadline)
                            @php
                                $isActive = in_array($order->status, ['paid', 'in_progress']);
                                $isOverdue = $isActive && now()->gt($order->delivery_deadline);
                                $hoursLeft = $isActive ? now()->diffInHours($order->delivery_deadline, false) : null;
                            @endphp
                            <span class="{{ $isOverdue ? 'text-red-600 font-bold' : ($hoursLeft !== null && $hoursLeft <= 24 ? 'text-orange-600 font-semibold' : 'text-gray-500 font-medium') }}">
                                {{ $order->delivery_deadline->format('d M') }}
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-circle text-red-500 ml-1" title="Terlambat"></i>
                                @elseif($hoursLeft !== null && $hoursLeft <= 24)
                                    <i class="fas fa-clock text-orange-500 ml-1" title="Segera"></i>
                                @endif
                            </span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('provider.orders.show', $order->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-inbox text-2xl text-blue-300"></i></div>
                        <h3 class="font-bold text-gray-800 mb-1">Belum ada pesanan</h3>
                        <p class="text-gray-400 text-sm font-medium">Buat layanan baru dan mulai terima pesanan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100/80">{{ $orders->links() }}</div>
    @endif
</div>

@endsection
