@extends('layouts.app')
@section('title', 'Dasbor Admin')
@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shrink-0">
                <i class="fas fa-chart-pie text-sm"></i>
            </div>
            Ringkasan Platform
        </h1>
        <p class="text-gray-500 text-sm mt-1 ml-[52px]">Pantau dan kelola marketplace Anda.</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php $statCards = [
        ['label' => 'Total Pengguna', 'value' => number_format($stats['total_users']), 'sub' => $stats['total_providers'].' penyedia, '.$stats['total_customers'].' pelanggan', 'subColor' => 'text-blue-600', 'icon' => 'fa-users', 'iconBg' => 'bg-blue-100 text-blue-600', 'borderColor' => 'border-blue-200'],
        ['label' => 'Layanan Aktif', 'value' => number_format($stats['active_services']), 'sub' => $stats['total_services'].' total layanan', 'subColor' => 'text-green-600', 'icon' => 'fa-briefcase', 'iconBg' => 'bg-green-100 text-green-600', 'borderColor' => 'border-green-200'],
        ['label' => 'Pendapatan Platform', 'value' => 'Rp '.number_format($stats['total_revenue'], 0, ',', '.'), 'sub' => $stats['completed_orders'].' pesanan selesai', 'subColor' => 'text-green-600', 'icon' => 'fa-coins', 'iconBg' => 'bg-emerald-100 text-emerald-600', 'borderColor' => 'border-emerald-200'],
        ['label' => 'Pesanan Tertunda', 'value' => $stats['pending_orders'], 'sub' => $stats['total_orders'].' total pesanan', 'subColor' => 'text-orange-500', 'icon' => 'fa-clock', 'iconBg' => 'bg-orange-100 text-orange-600', 'borderColor' => 'border-orange-200'],
    ]; @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border {{ $card['borderColor'] }} p-5 card-hover-lift">
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

{{-- Recent Users --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-4 border-b border-gray-50 gap-3">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-users text-gray-400 text-sm"></i> Pengguna Terbaru
        </h3>
        <div class="flex items-center gap-3">
            <form action="" method="GET" class="relative hidden sm:block">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari pengguna..." class="pl-8 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </form>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition flex items-center gap-1">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @foreach($recentUsers as $user)
        <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50/50 transition">
            @php $bgColors = ['A'=>'bg-red-100 text-red-600','B'=>'bg-blue-100 text-blue-600','C'=>'bg-green-100 text-green-600','D'=>'bg-purple-100 text-purple-600','E'=>'bg-orange-100 text-orange-600']; $letter = strtoupper(substr($user->name,0,1)); $bg = $bgColors[$letter] ?? 'bg-gray-200 text-gray-600'; @endphp
            <div class="w-9 h-9 rounded-full {{ $bg }} flex items-center justify-center text-sm font-bold shrink-0">
                {{ $letter }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</div>
                <div class="text-xs text-gray-400 truncate">{{ $user->email }}</div>
            </div>
            <div class="text-right shrink-0">
                @php $roleMap = ['admin' => 'Admin', 'provider' => 'Penyedia', 'customer' => 'Pelanggan']; @endphp
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $roleMap[$user->role] ?? ucfirst($user->role) }}</span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3 font-medium">Pengguna</th>
                    <th class="px-6 py-3 font-medium">Peran</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Bergabung</th>
                    <th class="px-6 py-3 font-medium text-right">Penghasilan</th>
                    <th class="px-6 py-3 font-medium w-10 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentUsers as $user)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @php $bgColors = ['A'=>'bg-red-100 text-red-600','B'=>'bg-blue-100 text-blue-600','C'=>'bg-green-100 text-green-600','D'=>'bg-purple-100 text-purple-600','E'=>'bg-orange-100 text-orange-600']; $letter = strtoupper(substr($user->name,0,1)); $bg = $bgColors[$letter] ?? 'bg-gray-200 text-gray-600'; @endphp
                            <div class="w-9 h-9 rounded-full {{ $bg }} flex items-center justify-center text-sm font-bold shrink-0">
                                {{ $letter }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $roleMap = ['admin' => 'Admin', 'provider' => 'Penyedia', 'customer' => 'Pelanggan']; @endphp
                        <span class="text-sm text-gray-600">{{ $roleMap[$user->role] ?? ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 text-right">
                        @if($user->isProvider() && $user->profile)
                            Rp {{ number_format($user->profile->total_earned ?? 0, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-20">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
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
