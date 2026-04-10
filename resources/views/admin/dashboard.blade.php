@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Platform Overview</h1>
        <p class="text-gray-500 text-sm mt-1">Monitor and manage your marketplace.</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-8">
    @php $statCards = [
        ['label' => 'Total Users', 'value' => number_format($stats['total_users']), 'sub' => $stats['total_providers'].' providers, '.$stats['total_customers'].' customers', 'subColor' => 'text-blue-600', 'icon' => 'fa-users', 'iconBg' => 'bg-blue-100 text-blue-600', 'borderColor' => 'border-blue-200'],
        ['label' => 'Active Services', 'value' => number_format($stats['active_services']), 'sub' => $stats['total_services'].' total services', 'subColor' => 'text-green-600', 'icon' => 'fa-briefcase', 'iconBg' => 'bg-green-100 text-green-600', 'borderColor' => 'border-green-200'],
        ['label' => 'Platform Revenue', 'value' => 'Rp '.number_format($stats['total_revenue'], 0, ',', '.'), 'sub' => $stats['completed_orders'].' completed orders', 'subColor' => 'text-green-600', 'icon' => 'fa-dollar-sign', 'iconBg' => 'bg-emerald-100 text-emerald-600', 'borderColor' => 'border-emerald-200'],
        ['label' => 'Pending Orders', 'value' => $stats['pending_orders'], 'sub' => $stats['total_orders'].' total orders', 'subColor' => 'text-orange-500', 'icon' => 'fa-clock', 'iconBg' => 'bg-orange-100 text-orange-600', 'borderColor' => 'border-orange-200'],
    ]; @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border {{ $card['borderColor'] }} p-5 hover:shadow-md transition">
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
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-100 gap-3">
        <h3 class="font-bold text-gray-900">Recent Users</h3>
        <div class="flex items-center gap-3">
            <form action="" method="GET" class="relative hidden sm:block">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search users..." class="pl-8 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </form>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold transition">View All</a>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-50">
        @foreach($recentUsers as $user)
        <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50/50 transition">
            @php $bgColors = ['A'=>'bg-red-100 text-red-600','B'=>'bg-blue-100 text-blue-600','C'=>'bg-green-100 text-green-600','D'=>'bg-purple-100 text-purple-600','E'=>'bg-orange-100 text-orange-600']; $letter = strtoupper(substr($user->name,0,1)); $bg = $bgColors[$letter] ?? 'bg-gray-200 text-gray-600'; @endphp
            <div class="w-9 h-9 rounded-full {{ $bg }} flex items-center justify-center text-sm font-bold shrink-0">
                {{ $letter }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</div>
                <div class="text-xs text-gray-400 truncate">{{ $user->email }}</div>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($user->role) }}</span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-6 py-3 font-medium">User</th>
                    <th class="px-6 py-3 font-medium">Role</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Joined</th>
                    <th class="px-6 py-3 font-medium">Earnings</th>
                    <th class="px-6 py-3 font-medium w-10">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentUsers as $user)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @php $bgColors = ['A'=>'bg-red-100 text-red-600','B'=>'bg-blue-100 text-blue-600','C'=>'bg-green-100 text-green-600','D'=>'bg-purple-100 text-purple-600','E'=>'bg-orange-100 text-orange-600']; $letter = strtoupper(substr($user->name,0,1)); $bg = $bgColors[$letter] ?? 'bg-gray-200 text-gray-600'; @endphp
                            <div class="w-9 h-9 rounded-full {{ $bg }} flex items-center justify-center text-sm font-bold">
                                {{ $letter }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($user->role) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        @if($user->isProvider() && $user->profile)
                            Rp {{ number_format($user->profile->total_earned ?? 0, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 text-gray-400 hover:text-gray-600 rounded transition">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-20">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View / Edit User</a>
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
