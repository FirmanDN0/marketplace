@extends('layouts.app')
@section('title', 'Chat CS - ' . ($conversation->subject ?? 'Percakapan'))
@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold text-gray-900 truncate">{{ $conversation->subject ?? 'Percakapan' }}</h1>
            <p class="text-xs text-gray-400">Dimulai {{ $conversation->created_at->format('d M Y, H:i') }}</p>
        </div>
        @php
            $hasAgentReply = $messages->contains(fn($m) => $m->isAgent());
            $statusConfig = match($conversation->status) {
                'ai'     => ['color' => 'bg-indigo-100 text-indigo-700', 'icon' => 'fa-robot', 'label' => 'AI'],
                'human'  => ($hasAgentReply ? ['color' => 'bg-green-100 text-green-700', 'icon' => 'fa-headset', 'label' => 'Ditangani Agen'] : ['color' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-clock', 'label' => 'Menunggu Agen']),
                'closed' => ['color' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-check-circle', 'label' => 'Selesai'],
                default  => ['color' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-circle', 'label' => $conversation->status],
            };
        @endphp
        <span class="{{ $statusConfig['color'] }} text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0"><i class="fas {{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['label'] }}</span>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Status Banners --}}
    @if($conversation->isHuman() && !$messages->contains(fn($m) => $m->isAgent()))
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3">
        <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="font-semibold text-amber-800 text-sm">Menunggu Agen CS Manusia</div>
            <div class="text-amber-700 text-xs">Permintaan Anda sudah diterima. Agen kami akan segera merespons.</div>
        </div>
    </div>
    @elseif($conversation->isClosed())
    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3">
        <div class="w-9 h-9 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-check-circle"></i></div>
        <div class="text-gray-600 text-sm font-medium">Percakapan ini telah ditutup</div>
    </div>
    @endif

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="height: 60vh;">
        {{-- Chat header bar --}}
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <i class="fas fa-comments"></i>
                <span>{{ $messages->count() }} pesan</span>
            </div>
            <div class="flex items-center gap-2">
                @if($conversation->isAi())
                <form action="{{ route('customer-service.escalate', $conversation->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Hubungkan ke CS manusia?')"
                            class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1.5">
                        <i class="fas fa-headset"></i> Minta CS Manusia
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-5" id="chat-messages">
            @forelse($messages->filter(fn($m) => trim($m->message) !== '') as $msg)
            @php
                $isUser  = $msg->isUser();
                $isAi    = $msg->isAi();
                $isAgent = $msg->isAgent();
            @endphp
            <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }} gap-3">
                @if(!$isUser)
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm mt-5 {{ $isAi ? 'bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600' : 'bg-gradient-to-br from-green-100 to-emerald-100 text-green-600' }}">
                    @if($isAi) <i class="fas fa-robot"></i>
                    @else <i class="fas fa-headset"></i>
                    @endif
                </div>
                @endif
                <div class="max-w-[75%]">
                    <div class="text-[11px] {{ $isUser ? 'text-right text-gray-400' : 'text-gray-400' }} mb-1 font-medium">
                        @if($isAi) <span class="text-indigo-500">AI Customer Service</span>
                        @elseif($isAgent) <span class="text-green-600">Agen CS {{ optional($msg->sender)->name }}</span>
                        @else {{ auth()->user()->name }}
                        @endif
                        <span class="text-gray-300 mx-1">·</span> {{ $msg->created_at->format('H:i') }}
                    </div>
                    <div class="{{ $isUser
                        ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md'
                        : ($isAi
                            ? 'bg-gradient-to-br from-indigo-50 to-purple-50 text-gray-800 rounded-2xl rounded-tl-md border border-indigo-100/60'
                            : 'bg-gradient-to-br from-green-50 to-emerald-50 text-gray-800 rounded-2xl rounded-tl-md border border-green-100/60')
                    }} px-4 py-3 text-sm whitespace-pre-wrap break-words leading-relaxed">
                        {!! preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e(trim($msg->message))) !!}
                    </div>
                </div>
                @if($isUser)
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-5">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12">
                <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-300 flex items-center justify-center mx-auto mb-3 text-2xl"><i class="fas fa-comments"></i></div>
                <p class="text-gray-400 text-sm">Belum ada pesan.</p>
            </div>
            @endforelse
        </div>
        
        {{-- Quick Templates (only for AI mode) --}}
        @if($conversation->isAi() && !$conversation->isClosed())
        <div class="px-5 py-3 border-t border-gray-100 bg-white overflow-x-auto no-scrollbar">
            <div class="flex gap-2 min-w-max pb-1">
                @foreach([
                    'Bagaimana cara memesan layanan?',
                    'Bagaimana cara mengisi saldo wallet?',
                    'Apa itu dana escrow?',
                    'Bagaimana jika hasil pekerjaan tidak sesuai?',
                    'Cara mendaftar jadi provider'
                ] as $template)
                <form action="{{ route('customer-service.message', $conversation->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="message" value="{{ $template }}">
                    <button type="submit" class="px-4 py-2 rounded-full border border-gray-200 text-[11px] font-medium text-gray-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition whitespace-nowrap bg-gray-50/50">
                        {{ $template }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Message Input --}}
        @unless($conversation->isClosed())
        <div class="border-t border-gray-100 p-4 bg-gray-50/60">
            @if($errors->has('message'))
                <div class="text-red-500 text-xs mb-2 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('message') }}</div>
            @endif
            <form action="{{ route('customer-service.message', $conversation->id) }}" method="POST" class="flex items-end gap-3">
                @csrf
                <textarea name="message" rows="2" placeholder="Ketik pesan Anda…" required maxlength="3000"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit()}"
                    class="flex-1 rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition"></textarea>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white w-11 h-11 rounded-xl font-medium text-sm transition flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div class="text-[11px] text-gray-400 mt-2 flex items-center gap-3">
                <span><kbd class="bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-mono">Enter</kbd> kirim</span>
                <span><kbd class="bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-mono">Shift+Enter</kbd> baris baru</span>
            </div>
        </div>
        @endunless
    </div>

    {{-- Hubungi CS Banner (only for AI mode) --}}
    @if($conversation->isAi())
    <div class="mt-4 bg-white rounded-2xl p-4 border border-indigo-100 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fas fa-headset"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">Belum puas dengan jawaban AI?</p>
                <p class="text-xs text-gray-500">Hubungkan langsung dengan tim Customer Service kami.</p>
            </div>
        </div>
        <form action="{{ route('customer-service.escalate', $conversation->id) }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Hubungkan ke CS manusia?')"
                    class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-lg shadow-indigo-200">
                Hubungi CS
            </button>
        </form>
    </div>
    @endif

    {{-- Bottom Actions --}}
    <div class="flex flex-wrap gap-3 mt-4">
        <a href="{{ route('customer-service.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-list"></i> Semua Percakapan
        </a>
        <a href="{{ route('customer-service.start') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> Percakapan Baru
        </a>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chat-messages');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endsection
