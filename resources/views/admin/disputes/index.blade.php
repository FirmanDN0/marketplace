@extends('layouts.app')
@section('title', 'Admin: Disputes')
@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Disputes</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" class="rounded-xl border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    @foreach(['open','under_review','resolved','closed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
                @if(request('status'))
                    <a href="{{ route('admin.disputes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Clear</a>
                @endif
            </form>
        </div>
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($disputes as $d)
            <a href="{{ route('admin.disputes.show', $d->id) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">{{ optional($d->order)->order_number }}</span>
                    @php $sc = match($d->status) { 'resolved','closed' => 'bg-green-100 text-green-700', 'under_review' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ str_replace('_',' ',$d->status) }}</span>
                </div>
                <div class="text-sm text-gray-600 truncate mb-1">{{ Str::limit($d->reason, 60) }}</div>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span>by {{ optional($d->opener)->name }}</span>
                    <span>{{ $d->created_at->format('M d, Y') }}</span>
                </div>
            </a>
            @empty
            <div class="px-4 py-8 text-center text-gray-400">No disputes.</div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Order #</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Opened By</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Reason</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Date</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($disputes as $d)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ optional($d->order)->order_number }}</td>
                    <td class="px-5 py-3 text-gray-700 hidden md:table-cell">{{ optional($d->opener)->name }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ Str::limit($d->reason, 60) }}</td>
                    <td class="px-5 py-3">
                        @php $sc = match($d->status) { 'resolved','closed' => 'bg-green-100 text-green-700', 'under_review' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ str_replace('_',' ',$d->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ $d->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3"><a href="{{ route('admin.disputes.show', $d->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No disputes.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($disputes->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $disputes->links() }}</div>
        @endif
    </div>
</div>
@endsection
