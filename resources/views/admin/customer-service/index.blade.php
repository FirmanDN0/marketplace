@extends('layouts.app')
@section('title', 'Customer Service - Admin')
@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-comments text-blue-500"></i> Customer Service</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola percakapan pengguna yang perlu bantuan manusia</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center mx-auto mb-2"><i class="fas fa-hourglass-half"></i></div>
            <div class="text-2xl font-bold text-gray-900">{{ $counts['human'] }}</div>
            <div class="text-xs text-gray-500">Menunggu Agen</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mx-auto mb-2"><i class="fas fa-robot"></i></div>
            <div class="text-2xl font-bold text-gray-900">{{ $counts['ai'] }}</div>
            <div class="text-xs text-gray-500">Ditangani AI</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-2"><i class="fas fa-check-circle"></i></div>
            <div class="text-2xl font-bold text-gray-900">{{ $counts['closed'] }}</div>
            <div class="text-xs text-gray-500">Selesai</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-2"><i class="fas fa-clipboard-list"></i></div>
            <div class="text-2xl font-bold text-gray-900">{{ $counts['all'] }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach(['human' => 'Menunggu Agen', 'ai' => 'Ditangani AI', 'closed' => 'Selesai', 'all' => 'Semua'] as $key => $label)
        @php $active = request('status', 'human') === $key; @endphp
        <a href="{{ route('admin.customer-service.index', ['status' => $key]) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $active ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600' }}">
            {{ $label }} ({{ $counts[$key] }})
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($conversations->isEmpty())
        <div class="py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-green-100 text-green-500 flex items-center justify-center mx-auto mb-4"><i class="fas fa-trophy text-2xl"></i></div>
            <h3 class="font-semibold text-gray-900 mb-1">Tidak ada percakapan dalam kategori ini</h3>
            <p class="text-sm text-gray-500">Semua berjalan lancar!</p>
        </div>
        @else
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @foreach($conversations as $conv)
            @php
                $statusColor = match($conv->status) { 'ai' => 'bg-indigo-100 text-indigo-700', 'human' => 'bg-yellow-100 text-yellow-700', 'closed' => 'bg-green-100 text-green-700', default => 'bg-gray-100 text-gray-600' };
                $statusIcon = match($conv->status) { 'ai' => 'fa-robot', 'human' => 'fa-hourglass-half', 'closed' => 'fa-check-circle', default => 'fa-circle' };
                $statusText = match($conv->status) { 'ai' => 'AI', 'human' => 'Agen', 'closed' => 'Selesai', default => $conv->status };
            @endphp
            <a href="{{ route('admin.customer-service.show', $conv->id) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">{{ optional($conv->user)->name }}</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                        <i class="fas {{ $statusIcon }} text-[10px]"></i> {{ $statusText }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 truncate mb-1">{{ $conv->subject ?? '—' }}</div>
                <div class="text-xs text-gray-400">{{ $conv->updated_at->diffForHumans() }}</div>
            </a>
            @endforeach
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Pengguna</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Topik</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Agen</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Pesan Terakhir</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Waktu</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @foreach($conversations as $conv)
                @php
                    $statusColor = match($conv->status) { 'ai' => 'bg-indigo-100 text-indigo-700', 'human' => 'bg-yellow-100 text-yellow-700', 'closed' => 'bg-green-100 text-green-700', default => 'bg-gray-100 text-gray-600' };
                    $statusIcon = match($conv->status) { 'ai' => 'fa-robot', 'human' => 'fa-hourglass-half', 'closed' => 'fa-check-circle', default => 'fa-circle' };
                    $statusText = match($conv->status) { 'ai' => 'AI', 'human' => 'Agen', 'closed' => 'Selesai', default => $conv->status };
                @endphp
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-900">{{ optional($conv->user)->name }}</div>
                        <div class="text-xs text-gray-400">{{ optional($conv->user)->email }}</div>
                    </td>
                    <td class="px-5 py-3 text-gray-700">{{ $conv->subject ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                            <i class="fas {{ $statusIcon }} text-[10px]"></i> {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ optional($conv->agent)->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500 max-w-[200px] truncate hidden md:table-cell">{{ optional($conv->lastMessage)->message ? Str::limit($conv->lastMessage->message, 60) : '—' }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs hidden md:table-cell">{{ $conv->updated_at->diffForHumans() }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.customer-service.show', $conv->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($conversations->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $conversations->appends(request()->query())->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
