@extends('layouts.app')
@section('title', 'Pesan')
@section('content')
<div class="min-h-screen py-8">
<div class="max-w-4xl mx-auto px-4 sm:px-6">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200/80 text-gray-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 shadow-sm transition-all duration-300 shrink-0" title="Kembali">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-500/20"><i class="fas fa-comments text-sm"></i></div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Percakapan</h1>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Tetap terhubung dengan klien dan penyedia jasa</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        @forelse($conversations as $conv)
            @php $other = $conv->otherParticipant(auth()->user()); @endphp
            @php $unread = $conv->unreadCount(auth()->user()); @endphp
            <a href="{{ route('messages.show', $conv->id) }}"
               class="flex items-center gap-4 px-5 py-4 hover:bg-blue-50/50 transition border-b border-gray-50 last:border-0 group">
                @if(optional($other)->avatar)
                    <img src="{{ Storage::url($other->avatar) }}" alt="{{ $other->name }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                        {{ strtoupper(substr(optional($other)->name ?? '?', 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="flex items-center gap-1 font-semibold text-gray-900 group-hover:text-blue-600 transition">
                            {{ optional($other)->name }}
                            <x-verified-badge :user="$other" />
                        </span>
                        @if($conv->service)
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full truncate max-w-[200px]">{{ Str::limit($conv->service->title,30) }}</span>
                        @endif
                        @if($unread)
                            <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unread }} new</span>
                        @endif
                    </div>
                    @if($conv->lastMessage)
                        <div class="text-sm text-gray-500 truncate mt-0.5">{{ Str::limit($conv->lastMessage->message_text, 70) }}</div>
                    @endif
                </div>
                <div class="text-xs text-gray-400 flex-shrink-0">{{ optional($conv->lastMessage?->created_at)->diffForHumans() }}</div>
            </a>
        @empty
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-comments text-2xl text-blue-300"></i></div>
                <h3 class="text-lg font-bold text-gray-700 mb-1">Belum ada percakapan</h3>
                <p class="text-gray-400 text-sm font-medium">Hubungi penyedia jasa dari halaman layanan untuk memulai.</p>
            </div>
        @endforelse
    </div>

    @if($conversations->hasPages())
    <div class="mt-6">{{ $conversations->links() }}</div>
    @endif

</div>
</div>
@endsection
