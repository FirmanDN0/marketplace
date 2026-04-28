@extends('layouts.app')
@section('title', 'Dasbor Penyedia')
@section('content')

@php
    $hour = now()->format('H');
    if($hour < 12) { $greeting = 'Selamat pagi'; $greetIcon = 'fa-sun'; $greetColor = 'from-blue-500 to-cyan-600'; }
    elseif($hour < 17) { $greeting = 'Selamat siang'; $greetIcon = 'fa-cloud-sun'; $greetColor = 'from-blue-600 to-blue-800'; }
    else { $greeting = 'Selamat malam'; $greetIcon = 'fa-moon'; $greetColor = 'from-slate-700 to-blue-900'; }
@endphp

{{-- Greeting Banner --}}
<div class="bg-gradient-to-r {{ $greetColor }} rounded-2xl p-6 md:p-8 mb-8 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/[0.06] rounded-full"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-white/[0.04] rounded-full"></div>
        <div class="absolute top-6 right-1/3 w-12 h-12 bg-white/[0.05] rounded-lg rotate-12"></div>
    </div>
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-white/70 text-sm mb-2">
                <i class="fas {{ $greetIcon }}"></i>
                <span>{{ $greeting }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ auth()->user()->name }}</h1>
            <p class="text-white/70 text-sm">Berikut perkembangan bisnis Anda hari ini.</p>
        </div>
        <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-800 font-semibold rounded-xl hover:bg-gray-50 transition text-sm shadow-lg shrink-0">
            <i class="fas fa-plus text-blue-600"></i> Buat Layanan Baru
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php $statCards = [
        ['label' => 'Saldo Tersedia', 'value' => 'Rp '.number_format($stats['balance'], 0, ',', '.'), 'sub' => 'Total: Rp '.number_format($stats['total_earned'], 0, ',', '.'), 'subColor' => 'text-green-600', 'icon' => 'fa-wallet', 'iconBg' => 'bg-green-100 text-green-600'],
        ['label' => 'Pesanan Aktif', 'value' => $stats['active_orders'], 'sub' => $stats['total_orders'].' total pesanan', 'subColor' => 'text-blue-600', 'icon' => 'fa-clipboard-check', 'iconBg' => 'bg-blue-100 text-blue-600'],
        ['label' => 'Pesanan Selesai', 'value' => $stats['completed_orders'], 'sub' => $stats['active_services'].' layanan aktif', 'subColor' => 'text-emerald-600', 'icon' => 'fa-check-circle', 'iconBg' => 'bg-purple-100 text-purple-600'],
        ['label' => 'Rating', 'value' => number_format($stats['avg_rating'], 1), 'sub' => 'Dari '.$stats['completed_orders'].' ulasan', 'subColor' => 'text-gray-500', 'icon' => 'fa-star', 'iconBg' => 'bg-yellow-100 text-yellow-600'],
    ]; @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 card-hover-lift">
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

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php $quickActions = [
        ['label' => 'Buat Layanan', 'icon' => 'fa-plus-circle', 'route' => route('provider.services.create'), 'color' => 'text-blue-600 bg-blue-50 hover:bg-blue-100'],
        ['label' => 'Kelola Pesanan', 'icon' => 'fa-box', 'route' => route('provider.orders.index'), 'color' => 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100'],
        ['label' => 'Penarikan Dana', 'icon' => 'fa-money-bill-wave', 'route' => route('provider.withdraw.index'), 'color' => 'text-amber-600 bg-amber-50 hover:bg-amber-100'],
        ['label' => 'Bantuan', 'icon' => 'fa-headset', 'route' => route('customer-service.index'), 'color' => 'text-violet-600 bg-violet-50 hover:bg-violet-100'],
    ]; @endphp
    @foreach($quickActions as $action)
    <a href="{{ $action['route'] }}" class="flex items-center gap-3 p-4 bg-white border border-gray-100 rounded-2xl {{ $action['color'] }} transition group">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $action['color'] }} shrink-0">
            <i class="fas {{ $action['icon'] }} group-hover:scale-110 transition-transform"></i>
        </div>
        <span class="text-sm font-semibold text-gray-700">{{ $action['label'] }}</span>
    </a>
    @endforeach
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-clock text-gray-400 text-sm"></i> Pesanan Terbaru
        </h3>
        <a href="{{ route('provider.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition flex items-center gap-1">
            Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @forelse($recentOrders as $order)
        <a href="{{ route('provider.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-gray-50/50 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-blue-600">#{{ $order->order_number }}</span>
                @php $statusMap = [
                    'completed' => ['Selesai', 'bg-green-100 text-green-700'],
                    'in_progress' => ['Dikerjakan', 'bg-blue-100 text-blue-700'],
                    'paid' => ['Dibayar', 'bg-blue-100 text-blue-700'],
                    'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
                    'disputed' => ['Sengketa', 'bg-red-100 text-red-700'],
                    'delivered' => ['Dikirim', 'bg-cyan-100 text-cyan-700'],
                    'requirements_submitted' => ['Brief Diterima', 'bg-indigo-100 text-indigo-700'],
                    'placed' => ['Pesanan Baru', 'bg-yellow-100 text-yellow-700'],
                ];
                $s = $statusMap[$order->status] ?? [ucfirst(str_replace('_',' ',$order->status)), 'bg-gray-100 text-gray-700'];
                @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $s[1] }}">{{ $s[0] }}</span>
            </div>
            <p class="text-sm text-gray-700 truncate mb-1">{{ optional($order->service)->title }}</p>
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>{{ optional($order->customer)->name }}</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</span>
            </div>
        </a>
        @empty
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Belum ada pesanan</h3>
            <p class="text-gray-400 text-sm mb-5 max-w-sm mx-auto">Buat layanan baru dan mulai terima pesanan dari pelanggan.</p>
            <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200/50">
                <i class="fas fa-plus"></i> Buat Layanan Baru
            </a>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">ID Pesanan</th>
                    <th class="px-6 py-3 font-medium">Layanan</th>
                    <th class="px-6 py-3 font-medium">Pembeli</th>
                    <th class="px-6 py-3 font-medium">Tenggat</th>
                    <th class="px-6 py-3 font-medium text-right">Pendapatan</th>
                    <th class="px-6 py-3 font-medium text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('provider.orders.show', $order->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                            #{{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 max-w-[200px] truncate font-medium">{{ optional($order->service)->title }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold shrink-0">
                                {{ strtoupper(substr(optional($order->customer)->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-600">{{ optional($order->customer)->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ optional($order->delivery_deadline)->format('d M Y') ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 text-right">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">
                        @php $statusMap = [
                            'completed' => ['Selesai', 'bg-green-100 text-green-700'],
                            'in_progress' => ['Dikerjakan', 'bg-blue-100 text-blue-700'],
                            'paid' => ['Dibayar', 'bg-blue-100 text-blue-700'],
                            'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
                            'disputed' => ['Sengketa', 'bg-red-100 text-red-700'],
                            'delivered' => ['Dikirim', 'bg-cyan-100 text-cyan-700'],
                            'requirements_submitted' => ['Brief Diterima', 'bg-indigo-100 text-indigo-700'],
                            'placed' => ['Pesanan Baru', 'bg-yellow-100 text-yellow-700'],
                        ];
                        $s = $statusMap[$order->status] ?? [ucfirst(str_replace('_',' ',$order->status)), 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $s[1] }}">{{ $s[0] }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-2xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Belum ada pesanan</h3>
                        <p class="text-gray-400 text-sm mb-5 max-w-sm mx-auto">Buat layanan baru dan mulai terima pesanan dari pelanggan.</p>
                        <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200/50">
                            <i class="fas fa-plus"></i> Buat Layanan Baru
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
