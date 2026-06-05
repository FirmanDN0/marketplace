@extends('layouts.app')
@section('title', 'Dasbor Admin')
@section('content')

{{-- Hero Header --}}
<div class="hero-gradient rounded-3xl p-6 md:p-8 mb-8 relative overflow-hidden shadow-xl">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/[0.05] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-white/[0.04] rounded-full float-shape-reverse"></div>
        <div class="absolute top-8 right-1/4 w-16 h-16 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute top-[20%] left-[10%] w-32 h-32 bg-indigo-500/20 rounded-full glow-orb animate-float-slow"></div>
        <div class="absolute bottom-[10%] right-[15%] w-40 h-40 bg-violet-500/15 rounded-full glow-orb animate-float-reverse"></div>
        <div class="absolute inset-0 shimmer-effect opacity-10"></div>
    </div>
    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white border border-white/10 shadow-lg">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Ringkasan Platform</h1>
                <p class="text-blue-200/50 text-sm font-medium mt-0.5">Pantau dan kelola marketplace Anda.</p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php $statCards = [
        ['label' => 'Total Pengguna', 'value' => number_format($stats['total_users']), 'sub' => $stats['total_providers'].' penyedia, '.$stats['total_customers'].' pelanggan', 'subColor' => 'text-blue-600', 'icon' => 'fa-users', 'gradient' => 'from-blue-500 to-indigo-600', 'shadow' => 'shadow-blue-500/20'],
        ['label' => 'Layanan Aktif', 'value' => number_format($stats['active_services']), 'sub' => $stats['total_services'].' total layanan', 'subColor' => 'text-emerald-600', 'icon' => 'fa-briefcase', 'gradient' => 'from-emerald-500 to-green-600', 'shadow' => 'shadow-emerald-500/20'],
        ['label' => 'Pendapatan Platform', 'value' => 'Rp '.number_format($stats['total_revenue'], 0, ',', '.'), 'sub' => $stats['completed_orders'].' pesanan selesai', 'subColor' => 'text-emerald-600', 'icon' => 'fa-coins', 'gradient' => 'from-violet-500 to-purple-600', 'shadow' => 'shadow-violet-500/20'],
        ['label' => 'Pesanan Tertunda', 'value' => $stats['pending_orders'], 'sub' => $stats['total_orders'].' total pesanan', 'subColor' => 'text-orange-500', 'icon' => 'fa-clock', 'gradient' => 'from-amber-500 to-orange-600', 'shadow' => 'shadow-amber-500/20'],
    ]; @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-3xl border border-gray-100/80 p-5 card-hover-lift shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-500">{{ $card['label'] }}</span>
            <div class="w-10 h-10 bg-gradient-to-br {{ $card['gradient'] }} text-white rounded-xl flex items-center justify-center shadow-md {{ $card['shadow'] }}">
                <i class="fas {{ $card['icon'] }} text-sm"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900 mb-1">{{ $card['value'] }}</div>
        <div class="text-xs {{ $card['subColor'] }} font-bold">{{ $card['sub'] }}</div>
    </div>
    @endforeach
</div>

{{-- Recent Users --}}
<div class="bg-white rounded-3xl border border-gray-100/80 overflow-hidden shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-5 border-b border-gray-100/80 gap-3">
        <h3 class="font-extrabold text-gray-900 flex items-center gap-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg flex items-center justify-center shadow-sm shadow-blue-500/20"><i class="fas fa-users text-xs"></i></div>
            Pengguna Terbaru
        </h3>
        <div class="flex items-center gap-3">
            <form action="" method="GET" class="relative hidden sm:block">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari pengguna..." class="pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200/80 rounded-xl text-sm w-52 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-medium">
            </form>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-bold transition flex items-center gap-1.5">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @foreach($recentUsers as $user)
        <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-3 px-5 py-4 hover:bg-blue-50/30 transition">
            @php $bgColors = ['A'=>'from-red-500 to-rose-600','B'=>'from-blue-500 to-indigo-600','C'=>'from-green-500 to-emerald-600','D'=>'from-purple-500 to-violet-600','E'=>'from-orange-500 to-amber-600']; $letter = strtoupper(substr($user->name,0,1)); $bg = $bgColors[$letter] ?? 'from-gray-400 to-gray-500'; @endphp
            <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $bg }} text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-sm">
                {{ $letter }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-gray-800 truncate">{{ $user->name }}</div>
                <div class="text-xs text-gray-400 truncate font-medium">{{ $user->email }}</div>
            </div>
            <div class="text-right shrink-0">
                @php $roleMap = ['admin' => 'Admin', 'provider' => 'Penyedia', 'customer' => 'Pelanggan']; @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $roleMap[$user->role] ?? ucfirst($user->role) }}</span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3.5 font-semibold">Pengguna</th>
                    <th class="px-6 py-3.5 font-semibold">Peran</th>
                    <th class="px-6 py-3.5 font-semibold">Status</th>
                    <th class="px-6 py-3.5 font-semibold">Bergabung</th>
                    <th class="px-6 py-3.5 font-semibold text-right">Penghasilan</th>
                    <th class="px-6 py-3.5 font-semibold w-10 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentUsers as $user)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @php $bgColors = ['A'=>'from-red-500 to-rose-600','B'=>'from-blue-500 to-indigo-600','C'=>'from-green-500 to-emerald-600','D'=>'from-purple-500 to-violet-600','E'=>'from-orange-500 to-amber-600']; $letter = strtoupper(substr($user->name,0,1)); $bg = $bgColors[$letter] ?? 'from-gray-400 to-gray-500'; @endphp
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $bg }} text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-sm">
                                {{ $letter }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $roleMap = ['admin' => 'Admin', 'provider' => 'Penyedia', 'customer' => 'Pelanggan']; @endphp
                        <span class="text-sm text-gray-600 font-medium">{{ $roleMap[$user->role] ?? ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">
                        @if($user->isProvider() && $user->profile)
                            Rp {{ number_format($user->profile->total_earned ?? 0, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-1 w-44 bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-20">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition font-medium">
                                    <i class="fas fa-eye text-gray-400 text-xs w-4"></i> Lihat / Edit
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
