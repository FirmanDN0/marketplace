@extends('layouts.app')
@section('title', 'Manage Users')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50 text-sm">
        <i class="fas fa-plus"></i> Create User
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    {{-- Filters --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                       class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <select name="role" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-600">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role')==='admin'?'selected':'' }}>Admin</option>
                <option value="provider" {{ request('role')==='provider'?'selected':'' }}>Provider</option>
                <option value="customer" {{ request('role')==='customer'?'selected':'' }}>Customer</option>
            </select>
            <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-600">
                <option value="">All Status</option>
                <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                <option value="suspended" {{ request('status')==='suspended'?'selected':'' }}>Suspended</option>
                <option value="banned" {{ request('status')==='banned'?'selected':'' }}>Banned</option>
            </select>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">Filter</button>
            @if(request()->hasAny(['search','role','status']))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-red-500 transition">Clear</a>
            @endif
        </form>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @forelse($users as $user)
        <div class="px-4 py-4">
            <div class="flex items-center gap-3 mb-2">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover">
                @else
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</div>
                    <div class="text-xs text-gray-400 truncate">{{ $user->email }}</div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-600">{{ ucfirst($user->role) }}</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : ($user->status === 'suspended' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition"><i class="fas fa-pen text-xs"></i></a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="px-4 py-12 text-center text-gray-400">No users found.</div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-4 lg:px-6 py-3 font-medium">Name</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Role</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Status</th>
                    <th class="px-4 lg:px-6 py-3 font-medium hidden md:table-cell">Joined</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover">
                            @else
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-600">{{ ucfirst($user->role) }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : ($user->status === 'suspended' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-500 hidden md:table-cell">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>

@endsection
