@extends('layouts.app')
@section('title', 'Dasbor Pelanggan')
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
        <div class="absolute top-8 right-1/4 w-16 h-16 bg-white/[0.05] rounded-xl rotate-12"></div>
    </div>
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-white/70 text-sm mb-2">
                <i class="fas {{ $greetIcon }}"></i>
                <span>{{ $greeting }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ auth()->user()->name }}</h1>
            <p class="text-white/70 text-sm">Kelola pesanan dan temukan layanan profesional terbaik.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-800 font-semibold rounded-xl hover:bg-gray-50 transition text-sm shadow-lg">
                <i class="fas fa-compass text-blue-600"></i> Jelajahi Layanan
            </a>
        </div>
    </div>
</div>

{{-- Wallet + Stats Row --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 mb-8">
    {{-- Wallet Card --}}
    <div class="lg:col-span-2 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/[0.05] rounded-full"></div>
            <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/[0.04] rounded-full"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <span class="text-white/70 text-sm font-medium">Saldo Dompet</span>
                </div>
                <a href="{{ route('wallet.index') }}" class="text-white/50 hover:text-white text-xs transition">
                    Detail <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                </a>
            </div>
            <div class="text-3xl font-bold mb-4">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</div>
            <a href="{{ route('wallet.topup.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 hover:bg-white/25 border border-white/20 rounded-xl text-sm font-medium transition backdrop-blur-sm">
                <i class="fas fa-plus text-xs"></i> Isi Saldo
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="lg:col-span-3 grid grid-cols-3 gap-4">
        @php $statCards = [
            ['label' => 'Total Pesanan', 'value' => $stats['total_orders'], 'icon' => 'fa-box', 'iconBg' => 'bg-blue-100 text-blue-600', 'sub' => $stats['active_orders'] . ' aktif'],
            ['label' => 'Selesai', 'value' => $stats['completed_orders'], 'icon' => 'fa-check-circle', 'iconBg' => 'bg-green-100 text-green-600', 'sub' => 'dari ' . $stats['total_orders'] . ' pesanan'],
            ['label' => 'Pengeluaran', 'value' => 'Rp ' . number_format($stats['total_spent'], 0, ',', '.'), 'icon' => 'fa-receipt', 'iconBg' => 'bg-purple-100 text-purple-600', 'sub' => 'total transaksi'],
        ]; @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 card-hover-lift">
            <div class="w-10 h-10 {{ $card['iconBg'] }} rounded-xl flex items-center justify-center mb-3">
                <i class="fas {{ $card['icon'] }}"></i>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-0.5">{{ $card['value'] }}</div>
            <div class="text-xs text-gray-400 font-medium">{{ $card['label'] }}</div>
            <div class="text-[11px] text-gray-400 mt-1">{{ $card['sub'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php $quickActions = [
        ['label' => 'Jelajahi Layanan', 'icon' => 'fa-compass', 'route' => route('services.index'), 'color' => 'text-blue-600 bg-blue-50 hover:bg-blue-100'],
        ['label' => 'Pesanan Saya', 'icon' => 'fa-box', 'route' => route('customer.orders.index'), 'color' => 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100'],
        ['label' => 'Favorit', 'icon' => 'fa-heart', 'route' => route('favorites.index'), 'color' => 'text-rose-600 bg-rose-50 hover:bg-rose-100'],
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
        <a href="{{ route('customer.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition flex items-center gap-1">
            Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @forelse($recentOrders as $order)
        <a href="{{ route('customer.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-gray-50/50 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-blue-600">{{ $order->order_number }}</span>
                @php $statusMap = [
                    'completed' => ['Selesai', 'bg-green-100 text-green-700'],
                    'in_progress' => ['Dikerjakan', 'bg-blue-100 text-blue-700'],
                    'paid' => ['Dibayar', 'bg-blue-100 text-blue-700'],
                    'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
                    'disputed' => ['Sengketa', 'bg-red-100 text-red-700'],
                    'delivered' => ['Dikirim', 'bg-cyan-100 text-cyan-700'],
                    'requirements_submitted' => ['Brief Terkirim', 'bg-indigo-100 text-indigo-700'],
                    'placed' => ['Menunggu', 'bg-yellow-100 text-yellow-700'],
                ];
                $s = $statusMap[$order->status] ?? [ucfirst(str_replace('_',' ',$order->status)), 'bg-gray-100 text-gray-700'];
                @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $s[1] }}">{{ $s[0] }}</span>
            </div>
            <p class="text-sm text-gray-700 truncate mb-1">{{ optional($order->service)->title }}</p>
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>{{ optional($order->provider)->name }}</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
            </div>
        </a>
        @empty
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Belum ada pesanan</h3>
            <p class="text-gray-400 text-sm mb-5 max-w-sm mx-auto">Jelajahi ribuan layanan profesional dan temukan yang tepat untuk kebutuhan Anda.</p>
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200/50">
                <i class="fas fa-compass"></i> Jelajahi Layanan
            </a>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">No. Pesanan</th>
                    <th class="px-6 py-3 font-medium">Layanan</th>
                    <th class="px-6 py-3 font-medium">Penyedia</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Harga</th>
                    <th class="px-6 py-3 font-medium text-right">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 max-w-[220px] truncate font-medium">{{ optional($order->service)->title }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                                {{ strtoupper(substr(optional($order->provider)->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-600">{{ optional($order->provider)->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $statusMap = [
                            'completed' => ['Selesai', 'bg-green-100 text-green-700'],
                            'in_progress' => ['Dikerjakan', 'bg-blue-100 text-blue-700'],
                            'paid' => ['Dibayar', 'bg-blue-100 text-blue-700'],
                            'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
                            'disputed' => ['Sengketa', 'bg-red-100 text-red-700'],
                            'delivered' => ['Dikirim', 'bg-cyan-100 text-cyan-700'],
                            'requirements_submitted' => ['Brief Terkirim', 'bg-indigo-100 text-indigo-700'],
                            'placed' => ['Menunggu', 'bg-yellow-100 text-yellow-700'],
                        ];
                        $s = $statusMap[$order->status] ?? [ucfirst(str_replace('_',' ',$order->status)), 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $s[1] }}">{{ $s[0] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 text-right">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400 text-right">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-2xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Belum ada pesanan</h3>
                        <p class="text-gray-400 text-sm mb-5 max-w-sm mx-auto">Jelajahi ribuan layanan profesional dan temukan yang tepat untuk kebutuhan Anda.</p>
                        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200/50">
                            <i class="fas fa-compass"></i> Jelajahi Layanan
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
