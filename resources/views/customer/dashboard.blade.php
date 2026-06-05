@extends('layouts.app')
@section('title', 'Dasbor Pelanggan')
@section('content')

@php
    $hour = now()->format('H');
    if($hour < 12) { $greeting = 'Selamat pagi'; $greetIcon = 'fa-sun'; }
    elseif($hour < 17) { $greeting = 'Selamat siang'; $greetIcon = 'fa-cloud-sun'; }
    else { $greeting = 'Selamat malam'; $greetIcon = 'fa-moon'; }
@endphp

{{-- Hero Greeting Banner --}}
<div class="hero-gradient rounded-3xl p-6 md:p-8 mb-8 relative overflow-hidden shadow-xl">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/[0.05] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-white/[0.04] rounded-full float-shape-reverse"></div>
        <div class="absolute top-8 right-1/4 w-16 h-16 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute top-[20%] left-[10%] w-32 h-32 bg-indigo-500/20 rounded-full glow-orb animate-float-slow"></div>
        <div class="absolute bottom-[10%] right-[15%] w-40 h-40 bg-violet-500/15 rounded-full glow-orb animate-float-reverse"></div>
        <div class="absolute inset-0 shimmer-effect opacity-10"></div>
    </div>
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-blue-200/60 text-sm mb-2 font-medium">
                <i class="fas {{ $greetIcon }}"></i>
                <span>{{ $greeting }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-1 tracking-tight">{{ auth()->user()->name }}</h1>
            <p class="text-blue-200/50 text-sm font-medium">Kelola pesanan dan temukan layanan profesional terbaik.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 backdrop-blur-sm text-white border border-white/20 font-bold rounded-2xl hover:bg-white/20 transition-all text-sm shadow-lg hover:-translate-y-0.5 active:translate-y-0 duration-300">
                <i class="fas fa-compass"></i> Jelajahi Layanan
            </a>
        </div>
    </div>
</div>

{{-- Wallet + Stats Row --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 mb-8">
    {{-- Wallet Card --}}
    <div class="lg:col-span-2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-3xl p-6 text-white relative overflow-hidden shadow-xl shadow-blue-600/15">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/[0.06] rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-28 h-28 bg-white/[0.04] rounded-full"></div>
            <div class="absolute top-1/2 right-1/3 w-12 h-12 bg-white/[0.03] rounded-xl rotate-12"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/10">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <span class="text-white/70 text-sm font-semibold">Saldo Dompet</span>
                </div>
                <a href="{{ route('wallet.index') }}" class="text-white/40 hover:text-white text-xs font-semibold transition-colors">
                    Detail <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                </a>
            </div>
            <div class="text-3xl font-extrabold mb-4 tracking-tight" data-rt-stat="balance">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</div>
            <a href="{{ route('wallet.topup.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25 border border-white/20 rounded-xl text-sm font-bold transition-all backdrop-blur-sm hover:-translate-y-0.5 active:translate-y-0 duration-300">
                <i class="fas fa-plus text-xs"></i> Isi Saldo
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="lg:col-span-3 grid grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl border border-gray-100/80 p-5 card-hover-lift shadow-sm">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl flex items-center justify-center mb-3 shadow-md shadow-blue-500/20"><i class="fas fa-box text-sm"></i></div>
            <div class="text-2xl font-extrabold text-gray-900 mb-0.5" data-rt-stat="total_orders">{{ $stats['total_orders'] }}</div>
            <div class="text-xs text-gray-400 font-semibold">Total Pesanan</div>
            <div class="text-[11px] text-blue-500 font-bold mt-1" data-rt-stat="active_orders">{{ $stats['active_orders'] }} aktif</div>
        </div>
        <div class="bg-white rounded-3xl border border-gray-100/80 p-5 card-hover-lift shadow-sm">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl flex items-center justify-center mb-3 shadow-md shadow-emerald-500/20"><i class="fas fa-check-circle text-sm"></i></div>
            <div class="text-2xl font-extrabold text-gray-900 mb-0.5" data-rt-stat="completed_orders">{{ $stats['completed_orders'] }}</div>
            <div class="text-xs text-gray-400 font-semibold">Selesai</div>
            <div class="text-[11px] text-emerald-500 font-bold mt-1">dari {{ $stats['total_orders'] }} pesanan</div>
        </div>
        <div class="bg-white rounded-3xl border border-gray-100/80 p-5 card-hover-lift shadow-sm">
            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 text-white rounded-xl flex items-center justify-center mb-3 shadow-md shadow-violet-500/20"><i class="fas fa-receipt text-sm"></i></div>
            <div class="text-2xl font-extrabold text-gray-900 mb-0.5" data-rt-stat="total_spent">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-400 font-semibold">Pengeluaran</div>
            <div class="text-[11px] text-violet-500 font-bold mt-1">total transaksi</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php $quickActions = [
        ['label' => 'Jelajahi Layanan', 'icon' => 'fa-compass', 'route' => route('services.index'), 'gradient' => 'from-blue-500 to-indigo-600', 'shadow' => 'shadow-blue-500/15'],
        ['label' => 'Pesanan Saya', 'icon' => 'fa-box', 'route' => route('customer.orders.index'), 'gradient' => 'from-emerald-500 to-green-600', 'shadow' => 'shadow-emerald-500/15'],
        ['label' => 'Favorit', 'icon' => 'fa-heart', 'route' => route('favorites.index'), 'gradient' => 'from-rose-500 to-pink-600', 'shadow' => 'shadow-rose-500/15'],
        ['label' => 'Bantuan', 'icon' => 'fa-headset', 'route' => route('customer-service.index'), 'gradient' => 'from-violet-500 to-purple-600', 'shadow' => 'shadow-violet-500/15'],
    ]; @endphp
    @foreach($quickActions as $action)
    <a href="{{ $action['route'] }}" class="flex items-center gap-3 p-4 bg-white border border-gray-100/80 rounded-3xl hover:border-gray-200 transition-all group card-hover-lift shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $action['gradient'] }} text-white flex items-center justify-center shrink-0 shadow-md {{ $action['shadow'] }} group-hover:scale-110 transition-transform duration-300">
            <i class="fas {{ $action['icon'] }} text-sm"></i>
        </div>
        <span class="text-sm font-bold text-gray-700">{{ $action['label'] }}</span>
    </a>
    @endforeach
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-3xl border border-gray-100/80 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100/80">
        <h3 class="font-extrabold text-gray-900 flex items-center gap-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg flex items-center justify-center shadow-sm shadow-blue-500/20"><i class="fas fa-clock text-xs"></i></div>
            Pesanan Terbaru
        </h3>
        <a href="{{ route('customer.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-bold transition flex items-center gap-1.5">
            Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @forelse($recentOrders as $order)
        <a href="{{ route('customer.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-blue-50/30 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-blue-600">{{ $order->order_number }}</span>
                @php $statusMap = [
                    'completed' => ['Selesai', 'bg-emerald-100 text-emerald-700'],
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
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s[1] }}">{{ $s[0] }}</span>
            </div>
            <p class="text-sm text-gray-700 truncate mb-1 font-medium">{{ optional($order->service)->title }}</p>
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>{{ optional($order->provider)->name }}</span>
                <span class="font-bold text-gray-800">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
            </div>
        </a>
        @empty
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-2xl text-blue-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada pesanan</h3>
            <p class="text-gray-400 text-sm mb-5 max-w-sm mx-auto font-medium">Jelajahi ribuan layanan profesional dan temukan yang tepat untuk kebutuhan Anda.</p>
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-blue-500 hover:to-indigo-500 transition-all shadow-lg shadow-blue-500/15">
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
                    <th class="px-6 py-3.5 font-semibold">No. Pesanan</th>
                    <th class="px-6 py-3.5 font-semibold">Layanan</th>
                    <th class="px-6 py-3.5 font-semibold">Penyedia</th>
                    <th class="px-6 py-3.5 font-semibold">Status</th>
                    <th class="px-6 py-3.5 font-semibold text-right">Harga</th>
                    <th class="px-6 py-3.5 font-semibold text-right">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 max-w-[220px] truncate font-medium">{{ optional($order->service)->title }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 shadow-sm">
                                {{ strtoupper(substr(optional($order->provider)->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-600 font-medium">{{ optional($order->provider)->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $statusMap = [
                            'completed' => ['Selesai', 'bg-emerald-100 text-emerald-700'],
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
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s[1] }}">{{ $s[0] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400 font-medium text-right">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-2xl text-blue-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada pesanan</h3>
                        <p class="text-gray-400 text-sm mb-5 max-w-sm mx-auto font-medium">Jelajahi ribuan layanan profesional dan temukan yang tepat untuk kebutuhan Anda.</p>
                        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-blue-500 hover:to-indigo-500 transition-all shadow-lg shadow-blue-500/15">
                            <i class="fas fa-compass"></i> Jelajahi Layanan
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const statsUrl = '/api/realtime/dashboard/stats';
    let polling = true;

    function formatRupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function updateStat(key, value) {
        const el = document.querySelector(`[data-rt-stat="${key}"]`);
        if (!el) return;

        const old = el.textContent;
        let newText = '';

        switch(key) {
            case 'balance':
            case 'total_spent':
                newText = formatRupiah(value);
                break;
            case 'total_orders':
                newText = String(value);
                break;
            case 'active_orders':
                newText = value + ' aktif';
                break;
            case 'completed_orders':
                newText = String(value);
                break;
            default:
                newText = String(value);
        }

        if (old !== newText) {
            el.textContent = newText;
            el.style.transition = 'color 0.3s';
            el.style.color = '#2563eb';
            setTimeout(() => { el.style.color = ''; }, 1500);
        }
    }

    async function pollStats() {
        if (!polling) return;
        try {
            const res = await fetch(statsUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.stats) {
                Object.entries(data.stats).forEach(([key, value]) => {
                    updateStat(key, value);
                });
            }
        } catch (e) { /* ignore */ }
    }

    setInterval(pollStats, 10000);

    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) pollStats();
    });
})();
</script>
@endpush

@endsection
