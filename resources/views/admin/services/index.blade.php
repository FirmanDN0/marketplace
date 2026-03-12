@extends('layouts.app')
@section('title', 'Admin: Services')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold text-gray-900">All Services</h1>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title..."
                       class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-600">
                <option value="">All Status</option>
                @foreach(['draft','active','paused','rejected','deleted'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.services.index') }}" class="px-4 py-2.5 text-sm text-gray-500 hover:text-red-500 transition">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-6 py-3 font-medium">Title</th>
                    <th class="px-6 py-3 font-medium">Provider</th>
                    <th class="px-6 py-3 font-medium">Category</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Rating</th>
                    <th class="px-6 py-3 font-medium">Orders</th>
                    <th class="px-6 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($services as $svc)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800 max-w-[250px] truncate">{{ $svc->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ optional($svc->provider)->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ optional($svc->category)->name }}</td>
                    <td class="px-6 py-4">
                        @php $sc = match($svc->status) { 'active' => 'bg-green-100 text-green-700', 'rejected','deleted' => 'bg-red-100 text-red-700', 'paused' => 'bg-yellow-100 text-yellow-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ ucfirst($svc->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <span class="flex items-center gap-1"><i class="fas fa-star text-yellow-400 text-xs"></i> {{ number_format($svc->avg_rating, 1) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $svc->total_orders }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.services.show', $svc->id) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition"><i class="fas fa-eye text-xs"></i></a>
                            <form method="POST" action="{{ route('admin.services.destroy', $svc->id) }}" onsubmit="return confirm('Delete this service?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition"><i class="fas fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($services->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $services->links() }}</div>
    @endif
</div>

@endsection
