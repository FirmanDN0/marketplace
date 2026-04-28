@extends('layouts.app')
@section('title', 'My Services')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold text-gray-900">My Services</h1>
    <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50 text-sm">
        <i class="fas fa-plus"></i> Create New Service
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @forelse($services as $svc)
        <div class="px-4 py-4">
            <div class="flex items-center justify-between mb-2">
                <a href="{{ route('provider.services.show', $svc) }}" class="text-sm font-medium text-gray-800 truncate flex-1 mr-2 hover:text-blue-600 transition">{{ $svc->title }}</a>
                @php $sc = match($svc->status) { 'active' => 'bg-green-100 text-green-700', 'paused' => 'bg-yellow-100 text-yellow-700', 'rejected','deleted' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ ucfirst($svc->status) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-gray-500">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1"><i class="fas fa-star text-yellow-400"></i> {{ number_format($svc->avg_rating, 1) }}</span>
                    <span>{{ $svc->total_orders }} orders</span>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('provider.services.edit', $svc) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition"><i class="fas fa-pen text-xs"></i></a>
                    <form method="POST" action="{{ route('provider.services.toggle', $svc) }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg transition {{ $svc->status === 'active' ? 'text-yellow-500 hover:bg-yellow-50' : 'text-green-500 hover:bg-green-50' }}">
                            <i class="fas {{ $svc->status === 'active' ? 'fa-pause' : 'fa-play' }} text-xs"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('provider.services.destroy', $svc) }}" onsubmit="return confirm('Delete this service permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="px-4 py-16 text-center">
            <div class="text-4xl text-gray-200 mb-3"><i class="fas fa-briefcase"></i></div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">No services yet</h3>
            <p class="text-gray-500 text-sm mb-4">Start listing your professional services to attract customers.</p>
            <a href="{{ route('provider.services.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">Create Your First Service</a>
        </div>
        @endforelse
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <th class="px-4 lg:px-6 py-3 font-medium">Title</th>
                    <th class="px-4 lg:px-6 py-3 font-medium hidden md:table-cell">Category</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Status</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Rating</th>
                    <th class="px-4 lg:px-6 py-3 font-medium hidden md:table-cell">Orders</th>
                    <th class="px-4 lg:px-6 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($services as $svc)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 lg:px-6 py-4 text-sm font-medium text-gray-800 max-w-[250px] truncate">
                        <a href="{{ route('provider.services.show', $svc) }}" class="hover:text-blue-600 transition">{{ $svc->title }}</a>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-500 hidden md:table-cell">{{ optional($svc->category)->name }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        @php $sc = match($svc->status) { 'active' => 'bg-green-100 text-green-700', 'paused' => 'bg-yellow-100 text-yellow-700', 'rejected','deleted' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ ucfirst($svc->status) }}</span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-600">
                        <span class="flex items-center gap-1"><i class="fas fa-star text-yellow-400 text-xs"></i> {{ number_format($svc->avg_rating, 1) }}</span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-600 hidden md:table-cell">{{ $svc->total_orders }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('provider.services.edit', $svc) }}" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <form method="POST" action="{{ route('provider.services.toggle', $svc) }}">
                                @csrf
                                <button type="submit" class="p-2 rounded-lg transition {{ $svc->status === 'active' ? 'text-yellow-500 hover:bg-yellow-50' : 'text-green-500 hover:bg-green-50' }}" title="{{ $svc->status === 'active' ? 'Pause' : 'Activate' }}">
                                    <i class="fas {{ $svc->status === 'active' ? 'fa-pause' : 'fa-play' }} text-xs"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('provider.services.destroy', $svc) }}" onsubmit="return confirm('Delete this service permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="text-4xl text-gray-200 mb-3"><i class="fas fa-briefcase"></i></div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">No services yet</h3>
                        <p class="text-gray-500 text-sm mb-4">Start listing your professional services to attract customers.</p>
                        <a href="{{ route('provider.services.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">Create Your First Service</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($services->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $services->links() }}</div>
    @endif
</div>

@endsection
