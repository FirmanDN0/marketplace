@extends('layouts.app')
@section('title', 'CS Chat - Admin')
@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customer-service.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $conversation->subject ?? 'Percakapan' }}</h1>
                <p class="text-sm text-gray-500">Pengguna: <span class="font-medium text-gray-700">{{ optional($conversation->user)->name }}</span> ({{ optional($conversation->user)->email }})</p>
            </div>
        </div>
        @php
            $hasAgentReply = $messages->contains(fn($m) => $m->isAgent());
            $statusColor = match($conversation->status) { 'ai' => 'bg-indigo-100 text-indigo-700', 'human' => ($hasAgentReply ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'), 'closed' => 'bg-gray-100 text-gray-600', default => 'bg-gray-100 text-gray-600' };
            $statusIcon = match($conversation->status) { 'ai' => 'fa-robot', 'human' => ($hasAgentReply ? 'fa-user' : 'fa-hourglass-half'), 'closed' => 'fa-check-circle', default => 'fa-circle' };
            $statusText = match($conversation->status) { 'ai' => 'AI', 'human' => ($hasAgentReply ? 'Ditangani Agen' : 'Menunggu Agen'), 'closed' => 'Selesai', default => $conversation->status };
        @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColor }}">
            <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Warning banner --}}
    @if($conversation->isHuman() && !$hasAgentReply)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-5 flex items-center gap-3">
        <span class="text-yellow-500"><i class="fas fa-exclamation-triangle"></i></span>
        <p class="text-sm text-yellow-700">
            Percakapan ini menunggu balasan agen.
            @if($conversation->agent_id) Ditangani oleh: <strong>{{ optional($conversation->agent)->name }}</strong>
            @else Belum ada agen yang ditugaskan.
            @endif
        </p>
    </div>
    @endif

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <div id="chat-messages" class="p-5 space-y-4 max-h-[65vh] overflow-y-auto">
            @forelse($messages as $msg)
            @php
                $isUser  = $msg->isUser();
                $isAi    = $msg->isAi();
                $isAgent = $msg->isAgent();
            @endphp
            <div class="flex items-start gap-3 {{ $isUser ? '' : '' }}">
                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                    {{ $isAi ? 'bg-indigo-100 text-indigo-600' : ($isAgent ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600') }}">
                    @if($isAi) <i class="fas fa-robot"></i>
                    @elseif($isAgent) <i class="fas fa-user"></i>
                    @else {{ strtoupper(substr(optional($conversation->user)->name ?? 'U', 0, 1)) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="text-xs text-gray-400 mb-1">
                        @if($isAi) <span class="font-medium text-indigo-600">AI Customer Service</span>
                        @elseif($isAgent) <span class="font-medium text-green-600">Agen: {{ optional($msg->sender)->name ?? 'Admin' }}</span>
                        @else <span class="font-medium text-blue-600">{{ optional($conversation->user)->name ?? 'User' }}</span>
                        @endif
                        · {{ $msg->created_at->format('d/m H:i') }}
                    </div>
                    <div class="rounded-2xl px-4 py-2.5 text-sm {{ $isAi ? 'bg-indigo-50 text-indigo-900' : ($isAgent ? 'bg-green-50 text-green-900' : 'bg-gray-100 text-gray-800') }} inline-block max-w-full whitespace-pre-wrap break-words">
                        {!! preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e(trim($msg->message))) !!}
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 py-8">Belum ada pesan.</p>
            @endforelse
        </div>

        {{-- Reply Form --}}
        @unless($conversation->isClosed())
        <div class="border-t border-gray-100 p-4">
            @if($errors->has('message'))
                <div class="text-red-500 text-xs mb-2">{{ $errors->first('message') }}</div>
            @endif
            <form action="{{ route('admin.customer-service.message', $conversation->id) }}" method="POST" class="flex items-end gap-3">
                @csrf
                <textarea name="message" rows="2" placeholder="Balas sebagai agen CS..." required maxlength="3000"
                          class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit()}"></textarea>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2 shrink-0">
                    Kirim <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>
        @endunless
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3">
        @if(!$conversation->isClosed())
        <form action="{{ route('admin.customer-service.close', $conversation->id) }}" method="POST"
              onsubmit="return confirm('Tutup percakapan ini?')">
            @csrf
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Tutup Percakapan
            </button>
        </form>
        @else
        <form action="{{ route('admin.customer-service.reopen', $conversation->id) }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Buka Kembali
            </button>
        </form>
        @endif
        <a href="{{ route('admin.customer-service.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition flex items-center gap-1.5">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chat-messages');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endsection
