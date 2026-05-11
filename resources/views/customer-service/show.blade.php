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
        <span id="cs-status-badge" class="{{ $statusConfig['color'] }} text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0"><i class="fas {{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['label'] }}</span>
        <span id="rt-live" class="text-xs text-green-500 font-medium flex items-center gap-1 opacity-0 transition-opacity duration-300 flex-shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
        </span>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Status Banners --}}
    <div id="banner-waiting" class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3 {{ ($conversation->isHuman() && !$hasAgentReply) ? '' : 'hidden' }}">
        <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="font-semibold text-amber-800 text-sm">Menunggu Agen CS Manusia</div>
            <div class="text-amber-700 text-xs">Permintaan Anda sudah diterima. Agen kami akan segera merespons.</div>
        </div>
    </div>
    <div id="banner-closed" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3 {{ $conversation->isClosed() ? '' : 'hidden' }}">
        <div class="w-9 h-9 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-check-circle"></i></div>
        <div class="text-gray-600 text-sm font-medium">Percakapan ini telah ditutup</div>
    </div>

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="height: 60vh;">
        {{-- Chat header bar --}}
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <i class="fas fa-comments"></i>
                <span id="msg-count">{{ $messages->count() }} pesan</span>
            </div>
            <div class="flex items-center gap-2" id="escalate-area">
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
        <div class="flex-1 overflow-y-auto p-5 space-y-6" id="chat-messages">
            @php 
                $lastDate = null;
                $validMessages = $messages->filter(fn($m) => trim($m->message) !== '');
            @endphp
            
            @forelse($validMessages as $msg)
                @php
                    $isUser  = $msg->isUser();
                    $isAi    = $msg->isAi();
                    $isAgent = $msg->isAgent();
                    $currentDate = $msg->created_at->format('Y-m-d');
                    $dateLabel = match($currentDate) {
                        now()->format('Y-m-d') => 'Hari Ini',
                        now()->subDay()->format('Y-m-d') => 'Kemarin',
                        default => $msg->created_at->format('d M Y'),
                    };
                @endphp

                {{-- Date Divider --}}
                @if($lastDate !== $currentDate)
                    <div class="flex justify-center my-4 rt-date-divider" data-date="{{ $currentDate }}">
                        <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm border border-gray-200">
                            {{ $dateLabel }}
                        </span>
                    </div>
                    @php $lastDate = $currentDate; @endphp
                @endif

                <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }} gap-3 rt-cs-msg" data-msg-id="{{ $msg->id }}">
                    @if(!$isUser)
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-sm mt-5 shadow-sm {{ $isAi ? 'bg-indigo-600 text-white' : 'bg-green-600 text-white' }}">
                        @if($isAi) <i class="fas fa-robot"></i>
                        @else <i class="fas fa-headset"></i>
                        @endif
                    </div>
                    @endif
                    
                    <div class="max-w-[80%]">
                        <div class="text-[10px] {{ $isUser ? 'text-right text-gray-400' : 'text-gray-400' }} mb-1 font-semibold uppercase tracking-tight">
                            @if($isAi) <span class="text-indigo-600">AI Assistant</span>
                            @elseif($isAgent) <span class="text-green-600">Customer Agent · {{ optional($msg->sender)->name }}</span>
                            @else <span class="text-blue-600">Anda</span>
                            @endif
                            <span class="text-gray-300 mx-1">•</span> {{ $msg->created_at->format('H:i') }}
                        </div>
                        <div class="{{ $isUser
                            ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none shadow-md shadow-blue-100'
                            : ($isAi
                                ? 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-indigo-100 shadow-sm'
                                : 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-green-100 shadow-sm')
                        }} px-4 py-3 text-sm whitespace-pre-wrap break-words leading-relaxed">
                            {!! preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e(trim($msg->message))) !!}
                        </div>
                    </div>

                    @if($isUser)
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-5 border border-blue-200 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    @endif
                </div>
            @empty
            <div class="text-center py-12" id="cs-empty">
                <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-300 flex items-center justify-center mx-auto mb-3 text-2xl"><i class="fas fa-comments"></i></div>
                <p class="text-gray-400 text-sm">Belum ada pesan.</p>
            </div>
            @endforelse
        </div>
        
        {{-- Quick Templates (only for AI mode) --}}
        <div id="quick-templates" class="{{ ($conversation->isAi() && !$conversation->isClosed()) ? '' : 'hidden' }}">
        <div class="px-5 py-3 border-t border-gray-100 bg-white overflow-x-auto no-scrollbar">
            <div class="flex gap-2 min-w-max pb-1">
                @foreach([
                    'Bagaimana cara memesan layanan?',
                    'Bagaimana cara mengisi saldo wallet?',
                    'Apa itu dana escrow?',
                    'Bagaimana jika hasil pekerjaan tidak sesuai?',
                    'Cara mendaftar jadi provider'
                ] as $template)
                <button type="button" class="cs-quick-btn px-4 py-2 rounded-full border border-gray-200 text-[11px] font-medium text-gray-600 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition whitespace-nowrap bg-gray-50/50" data-template="{{ $template }}">
                    {{ $template }}
                </button>
                @endforeach
            </div>
        </div>
        </div>

        {{-- Message Input --}}
        <div id="cs-input-area" class="{{ $conversation->isClosed() ? 'hidden' : '' }}">
        <div class="border-t border-gray-100 p-4 bg-gray-50/60">
            <div id="cs-form-error" class="text-red-500 text-xs mb-2 hidden items-center gap-1"><i class="fas fa-exclamation-circle"></i> <span></span></div>
            <form id="cs-form" class="flex items-end gap-3">
                @csrf
                <textarea id="cs-input" name="message" rows="2" placeholder="Ketik pesan Anda…" required maxlength="3000"
                    class="flex-1 rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition"></textarea>
                <button type="submit" id="cs-send-btn" class="bg-blue-600 hover:bg-blue-700 text-white w-11 h-11 rounded-xl font-medium text-sm transition flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div class="text-[11px] text-gray-400 mt-2 flex items-center gap-3">
                <span><kbd class="bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-mono">Enter</kbd> kirim</span>
                <span><kbd class="bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-mono">Shift+Enter</kbd> baris baru</span>
            </div>
        </div>
        </div>
    </div>

    {{-- Hubungi CS Banner (only for AI mode) --}}
    <div id="cs-escalate-banner" class="{{ $conversation->isAi() ? '' : 'hidden' }}">
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
    </div>

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

@push('scripts')
<script>
(() => {
    const convId     = {{ $conversation->id }};
    const pollUrl    = '/api/realtime/cs/' + convId + '/poll';
    const sendUrl    = '/api/realtime/cs/' + convId + '/send';
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
    const chatBox    = document.getElementById('chat-messages');
    const csForm     = document.getElementById('cs-form');
    const csInput    = document.getElementById('cs-input');
    const csSendBtn  = document.getElementById('cs-send-btn');
    const rtLive     = document.getElementById('rt-live');
    const csEmpty    = document.getElementById('cs-empty');
    const userInitial = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';
    const userName    = '{{ auth()->user()->name }}';

    let lastId   = {{ $messages->last()?->id ?? 0 }};
    let polling  = true;
    let sending  = false;
    let currentStatus = '{{ $conversation->status }}';
    let lastDateShown = '{{ $messages->isNotEmpty() ? $messages->last()->created_at->format("Y-m-d") : "" }}';

    // Scroll to bottom
    function scrollBottom() { chatBox.scrollTop = chatBox.scrollHeight; }
    scrollBottom();

    // Show live indicator
    setTimeout(() => { rtLive.style.opacity = '1'; }, 500);

    // Render a CS message
    function renderCsMessage(msg) {
        if (csEmpty) csEmpty.remove();

        // Date divider
        if (msg.date && msg.date !== lastDateShown) {
            const today = new Date().toISOString().slice(0,10);
            const yesterday = new Date(Date.now() - 86400000).toISOString().slice(0,10);
            let label = msg.date === today ? 'Hari Ini' : (msg.date === yesterday ? 'Kemarin' : msg.date);
            
            if (!document.querySelector(`[data-date="${msg.date}"]`)) {
                const divider = document.createElement('div');
                divider.className = 'flex justify-center my-4 rt-date-divider';
                divider.dataset.date = msg.date;
                divider.innerHTML = `<span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm border border-gray-200">${label}</span>`;
                chatBox.appendChild(divider);
            }
            lastDateShown = msg.date;
        }

        const wrapper = document.createElement('div');
        wrapper.className = `flex ${msg.is_user ? 'justify-end' : 'justify-start'} gap-3 rt-cs-msg`;
        wrapper.dataset.msgId = msg.id;
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(10px)';
        wrapper.style.transition = 'opacity 0.3s, transform 0.3s';

        let avatarLeft = '';
        let avatarRight = '';

        if (!msg.is_user) {
            const avatarBg = msg.is_ai ? 'bg-indigo-600 text-white' : 'bg-green-600 text-white';
            const avatarIcon = msg.is_ai ? 'fa-robot' : 'fa-headset';
            avatarLeft = `<div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-sm mt-5 shadow-sm ${avatarBg}"><i class="fas ${avatarIcon}"></i></div>`;
        } else {
            avatarRight = `<div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-5 border border-blue-200 shadow-sm">${userInitial}</div>`;
        }

        let labelHtml = '';
        if (msg.is_ai) labelHtml = '<span class="text-indigo-600">AI Assistant</span>';
        else if (msg.is_agent) labelHtml = `<span class="text-green-600">Customer Agent · ${msg.sender_name}</span>`;
        else labelHtml = '<span class="text-blue-600">Anda</span>';

        let bubbleClass = msg.is_user
            ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none shadow-md shadow-blue-100'
            : (msg.is_ai
                ? 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-indigo-100 shadow-sm'
                : 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-green-100 shadow-sm');

        wrapper.innerHTML = `
            ${avatarLeft}
            <div class="max-w-[80%]">
                <div class="text-[10px] ${msg.is_user ? 'text-right text-gray-400' : 'text-gray-400'} mb-1 font-semibold uppercase tracking-tight">
                    ${labelHtml}
                    <span class="text-gray-300 mx-1">•</span> ${msg.time}
                </div>
                <div class="${bubbleClass} px-4 py-3 text-sm whitespace-pre-wrap break-words leading-relaxed">
                    ${msg.message}
                </div>
            </div>
            ${avatarRight}`;

        chatBox.appendChild(wrapper);

        requestAnimationFrame(() => {
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });

        scrollBottom();
    }

    // Update status UI
    function updateStatusUI(status) {
        if (status === currentStatus) return;
        currentStatus = status;

        const badge = document.getElementById('cs-status-badge');
        const bannerWaiting = document.getElementById('banner-waiting');
        const bannerClosed = document.getElementById('banner-closed');
        const inputArea = document.getElementById('cs-input-area');
        const templates = document.getElementById('quick-templates');
        const escalate = document.getElementById('cs-escalate-banner');

        if (status === 'closed') {
            badge.className = 'bg-gray-100 text-gray-500 text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0';
            badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Selesai';
            bannerWaiting.classList.add('hidden');
            bannerClosed.classList.remove('hidden');
            inputArea.classList.add('hidden');
            templates.classList.add('hidden');
            escalate.classList.add('hidden');
        } else if (status === 'human') {
            badge.className = 'bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full flex-shrink-0';
            badge.innerHTML = '<i class="fas fa-clock mr-1"></i>Menunggu Agen';
            templates.classList.add('hidden');
            escalate.classList.add('hidden');
        }
    }

    // Typing indicator
    function showTypingIndicator() {
        let existing = document.getElementById('typing-indicator');
        if (existing) return;
        
        const indicator = document.createElement('div');
        indicator.id = 'typing-indicator';
        indicator.className = 'flex justify-start gap-3';
        indicator.innerHTML = `
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-sm mt-2 shadow-sm bg-indigo-600 text-white"><i class="fas fa-robot"></i></div>
            <div class="bg-white border border-indigo-100 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>`;
        chatBox.appendChild(indicator);
        scrollBottom();
    }

    function removeTypingIndicator() {
        const el = document.getElementById('typing-indicator');
        if (el) el.remove();
    }

    // Poll for new messages
    async function poll() {
        if (!polling) return;
        try {
            const res = await fetch(`${pollUrl}?after=${lastId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.messages && data.messages.length > 0) {
                removeTypingIndicator();
                data.messages.forEach(msg => {
                    if (!document.querySelector(`[data-msg-id="${msg.id}"]`)) {
                        renderCsMessage(msg);
                    }
                });
                lastId = data.last_id;
            }

            if (data.status) updateStatusUI(data.status);
        } catch (e) { /* ignore */ }
    }

    // Poll every 3 seconds
    setInterval(poll, 3000);

    // Send message via AJAX
    csForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (sending) return;

        const text = csInput.value.trim();
        if (!text) return;

        sending = true;
        csSendBtn.disabled = true;
        csSendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const res = await fetch(`${sendUrl}?after=${lastId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message: text })
            });

            if (res.ok) {
                const data = await res.json();
                csInput.value = '';

                // Show typing indicator for AI mode
                if (currentStatus === 'ai') {
                    showTypingIndicator();
                }

                if (data.messages && data.messages.length > 0) {
                    removeTypingIndicator();
                    data.messages.forEach(msg => {
                        if (!document.querySelector(`[data-msg-id="${msg.id}"]`)) {
                            renderCsMessage(msg);
                        }
                    });
                    lastId = data.last_id;
                }

                if (data.status) updateStatusUI(data.status);
            } else {
                const err = await res.json();
                const errBox = document.getElementById('cs-form-error');
                errBox.querySelector('span').textContent = err.error || 'Gagal mengirim pesan.';
                errBox.classList.remove('hidden');
                errBox.classList.add('flex');
                setTimeout(() => { errBox.classList.add('hidden'); errBox.classList.remove('flex'); }, 5000);
            }
        } catch (e) { /* ignore */ }
        finally {
            sending = false;
            csSendBtn.disabled = false;
            csSendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            csInput.focus();
        }
    });

    // Enter to send
    csInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            csForm.dispatchEvent(new Event('submit'));
        }
    });

    // Quick template buttons
    document.querySelectorAll('.cs-quick-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            csInput.value = btn.dataset.template;
            csForm.dispatchEvent(new Event('submit'));
        });
    });

    // Visibility handling
    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) poll();
    });
})();
</script>
@endpush
@endsection
