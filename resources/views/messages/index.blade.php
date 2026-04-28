@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<div class="min-h-screen py-8">
<div class="max-w-4xl mx-auto px-4 sm:px-6">


    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Conversations</h1>
        <p class="text-gray-500 text-sm mt-1">Stay in touch with your clients and providers</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($conversations as $conv)
            @php $other = $conv->otherParticipant(auth()->user()); @endphp
            @php $unread = $conv->unreadCount(auth()->user()); @endphp
            <a href="{{ route('messages.show', $conv->id) }}"
               class="flex items-center gap-4 px-5 py-4 hover:bg-blue-50/50 transition border-b border-gray-50 last:border-0 group">
                @if(optional($other)->avatar)
                    <img src="{{ Storage::url($other->avatar) }}" alt="{{ $other->name }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr(optional($other)->name ?? '?', 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ optional($other)->name }}</span>
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
                <div class="text-5xl text-gray-300 mb-4"><i class="fas fa-comments"></i></div>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">No conversations yet</h3>
                <p class="text-gray-500 text-sm">Message a provider from a service page to start a conversation.</p>
            </div>
        @endforelse
    </div>

    @if($conversations->hasPages())
    <div class="mt-6">{{ $conversations->links() }}</div>
    @endif

</div>
</div>
@endsection
