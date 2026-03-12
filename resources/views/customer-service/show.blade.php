@extends('layouts.app')
@section('title', 'Chat CS - ' . ($conversation->subject ?? 'Percakapan'))
@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('customer-service.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold text-gray-900 truncate">{{ $conversation->subject ?? 'Percakapan' }}</h1>
        </div>
        @php
            $hasAgentReply = $messages->contains(fn($m) => $m->isAgent());
            $statusColor = match($conversation->status) { 'ai' => 'bg-indigo-100 text-indigo-700', 'human' => ($hasAgentReply ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'), 'closed' => 'bg-gray-100 text-gray-500', default => 'bg-gray-100 text-gray-500' };
            $statusLabel = match($conversation->status) { 'ai' => '<i class="fas fa-robot"></i> AI', 'human' => ($hasAgentReply ? '<i class="fas fa-user"></i> Ditangani Agen' : '<i class="fas fa-user"></i> Menunggu Agen'), 'closed' => '<i class="fas fa-check-circle"></i> Selesai', default => $conversation->status };
        @endphp
        <span class="{{ $statusColor }} text-xs font-semibold px-3 py-1.5 rounded-full">{!! $statusLabel !!}</span>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Status Banner --}}
    @if($conversation->isHuman() && !$messages->contains(fn($m) => $m->isAgent()))
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3">
        <span class="text-yellow-500 text-xl"><i class="fas fa-hourglass-half"></i></span>
        <div>
            <div class="font-semibold text-yellow-800 text-sm">Menunggu Agen CS Manusia</div>
            <div class="text-yellow-700 text-xs">Permintaan Anda sudah diterima. Agen kami akan segera merespons.</div>
        </div>
    </div>
    @elseif($conversation->isClosed())
    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3">
        <span class="text-gray-400 text-xl"><i class="fas fa-check-circle"></i></span>
        <div class="text-gray-600 text-sm font-medium">Percakapan Ini Telah Ditutup</div>
    </div>
    @endif

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="height: 60vh;">
        <div class="flex-1 overflow-y-auto p-5 space-y-4" id="chat-messages">
            @forelse($messages->filter(fn($m) => trim($m->message) !== '') as $msg)
            @php
                $isUser  = $msg->isUser();
                $isAi    = $msg->isAi();
                $isAgent = $msg->isAgent();
            @endphp
            <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }} gap-3">
                @if(!$isUser)
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm {{ $isAi ? 'bg-indigo-100 text-indigo-600' : 'bg-green-100 text-green-600' }}">
                    @if($isAi) <i class="fas fa-robot"></i>
                    @else <i class="fas fa-user"></i>
                    @endif
                </div>
                @endif
                <div class="max-w-[75%]">
                    <div class="text-xs {{ $isUser ? 'text-right text-gray-400' : 'text-gray-400' }} mb-1">
                        @if($isAi) AI Customer Service
                        @elseif($isAgent) Agen CS {{ optional($msg->sender)->name }}
                        @else {{ auth()->user()->name }}
                        @endif
                        · {{ $msg->created_at->format('H:i') }}
                    </div>
                    <div class="{{ $isUser ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md' : ($isAi ? 'bg-indigo-50 text-gray-800 rounded-2xl rounded-tl-md' : 'bg-green-50 text-gray-800 rounded-2xl rounded-tl-md') }} px-4 py-3 text-sm whitespace-pre-wrap break-words">
                        {!! preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e(trim($msg->message))) !!}
                    </div>
                </div>
                @if($isUser)
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">Belum ada pesan.</div>
            @endforelse
        </div>

        {{-- Message Input --}}
        @unless($conversation->isClosed())
        <div class="border-t border-gray-100 p-4 bg-gray-50">
            @if($errors->has('message'))
                <div class="text-red-500 text-xs mb-2">{{ $errors->first('message') }}</div>
            @endif
            <form action="{{ route('customer-service.message', $conversation->id) }}" method="POST" class="flex items-end gap-3">
                @csrf
                <textarea name="message" rows="2" placeholder="Ketik pesan Anda..." required maxlength="3000"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit()}"
                    class="flex-1 rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2">
                    Kirim <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            <div class="text-xs text-gray-400 mt-2">Enter untuk kirim · Shift+Enter untuk baris baru</div>
        </div>
        @endunless
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3 mt-4">
        @if($conversation->isAi())
        <form action="{{ route('customer-service.escalate', $conversation->id) }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Hubungkan ke CS manusia?')"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="fas fa-user"></i> Minta CS Manusia
            </button>
        </form>
        @endif
        <a href="{{ route('customer-service.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Semua Percakapan
        </a>
        <a href="{{ route('customer-service.create') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Percakapan Baru
        </a>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chat-messages');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endsection
