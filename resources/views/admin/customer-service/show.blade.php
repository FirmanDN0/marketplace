@extends('layouts.app')
@section('title', 'CS Chat - Admin')
@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $conversation->subject ?? 'Percakapan' }}</h1>
                <p class="text-sm text-gray-500">Pengguna: <span class="font-medium text-gray-700">{{ optional($conversation->user)->name }}</span> ({{ optional($conversation->user)->email }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php
                $hasAgentReply = $messages->contains(fn($m) => $m->isAgent());
                $statusColor = match($conversation->status) { 'ai' => 'bg-indigo-100 text-indigo-700', 'human' => ($hasAgentReply ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'), 'closed' => 'bg-gray-100 text-gray-600', default => 'bg-gray-100 text-gray-600' };
                $statusIcon = match($conversation->status) { 'ai' => 'fa-robot', 'human' => ($hasAgentReply ? 'fa-user' : 'fa-hourglass-half'), 'closed' => 'fa-check-circle', default => 'fa-circle' };
                $statusText = match($conversation->status) { 'ai' => 'AI', 'human' => ($hasAgentReply ? 'Ditangani Agen' : 'Menunggu Agen'), 'closed' => 'Selesai', default => $conversation->status };
            @endphp
            <span id="admin-cs-badge" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                <i class="fas {{ $statusIcon }}"></i> {{ $statusText }}
            </span>
            <span id="rt-live" class="text-xs text-green-500 font-medium flex items-center gap-1 opacity-0 transition-opacity duration-300 flex-shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Warning banner --}}
    <div id="admin-banner-waiting" class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-5 flex items-center gap-3 {{ ($conversation->isHuman() && !$hasAgentReply) ? '' : 'hidden' }}">
        <span class="text-yellow-500"><i class="fas fa-exclamation-triangle"></i></span>
        <p class="text-sm text-yellow-700">
            Percakapan ini menunggu balasan agen.
            @if($conversation->agent_id) Ditangani oleh: <strong>{{ optional($conversation->agent)->name }}</strong>
            @else Belum ada agen yang ditugaskan.
            @endif
        </p>
    </div>

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <div id="chat-messages" class="p-5 space-y-6 max-h-[65vh] overflow-y-auto">
            @php 
                $lastDate = null;
            @endphp
            
            @forelse($messages as $msg)
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

                <div class="flex items-start gap-3 rt-cs-msg" data-msg-id="{{ $msg->id }}">
                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 shadow-sm
                        {{ $isAi ? 'bg-indigo-600 text-white' : ($isAgent ? 'bg-green-600 text-white' : 'bg-blue-600 text-white') }}">
                        @if($isAi) <i class="fas fa-robot"></i>
                        @elseif($isAgent) <i class="fas fa-headset"></i>
                        @else {{ strtoupper(substr(optional($conversation->user)->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] text-gray-400 mb-1 font-semibold uppercase tracking-tight">
                            @if($isAi) <span class="text-indigo-600">AI Assistant</span>
                            @elseif($isAgent) <span class="text-green-600">Agen: {{ optional($msg->sender)->name ?? 'Admin' }}</span>
                            @else <span class="text-blue-600">{{ optional($conversation->user)->name ?? 'User' }}</span>
                            @endif
                            <span class="text-gray-300 mx-1">•</span> {{ $msg->created_at->format('H:i') }}
                        </div>
                        <div class="rounded-2xl px-4 py-2.5 text-sm shadow-sm border {{ $isAi ? 'bg-indigo-50/50 border-indigo-100 text-gray-800' : ($isAgent ? 'bg-green-50/50 border-green-100 text-gray-800' : 'bg-gray-50 border-gray-200 text-gray-800') }} inline-block max-w-[90%] whitespace-pre-wrap break-words">
                            {!! preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e(trim($msg->message))) !!}
                        </div>
                    </div>
                </div>
            @empty
            <p class="text-center text-gray-400 py-8" id="admin-cs-empty">Belum ada pesan.</p>
            @endforelse
        </div>

        {{-- Reply Form --}}
        <div id="admin-reply-area" class="{{ $conversation->isClosed() ? 'hidden' : '' }}">
        <div class="border-t border-gray-100 p-4">
            <div id="admin-form-error" class="text-red-500 text-xs mb-2 hidden"></div>
            <form id="admin-cs-form" class="flex items-end gap-3">
                @csrf
                <textarea id="admin-cs-input" name="message" rows="2" placeholder="Balas sebagai agen CS..." required maxlength="3000"
                          class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                <button type="submit" id="admin-cs-send" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2 shrink-0">
                    Kirim <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>
        </div>
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
    </div>
</div>

@push('scripts')
<script>
(() => {
    const convId     = {{ $conversation->id }};
    const pollUrl    = '/api/realtime/cs-admin/' + convId + '/poll';
    const sendUrl    = '/api/realtime/cs-admin/' + convId + '/send';
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
    const chatBox    = document.getElementById('chat-messages');
    const form       = document.getElementById('admin-cs-form');
    const input      = document.getElementById('admin-cs-input');
    const sendBtn    = document.getElementById('admin-cs-send');
    const rtLive     = document.getElementById('rt-live');
    const emptyEl    = document.getElementById('admin-cs-empty');
    const userInitial = '{{ strtoupper(substr(optional($conversation->user)->name ?? "U", 0, 1)) }}';

    let lastId   = {{ $messages->last()?->id ?? 0 }};
    let polling  = true;
    let sending  = false;
    let currentStatus = '{{ $conversation->status }}';
    let lastDateShown = '{{ $messages->isNotEmpty() ? $messages->last()->created_at->format("Y-m-d") : "" }}';

    function scrollBottom() { chatBox.scrollTop = chatBox.scrollHeight; }
    scrollBottom();
    setTimeout(() => { rtLive.style.opacity = '1'; }, 500);

    function renderAdminMsg(msg) {
        if (emptyEl) emptyEl.remove();

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
        wrapper.className = 'flex items-start gap-3 rt-cs-msg';
        wrapper.dataset.msgId = msg.id;
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(10px)';
        wrapper.style.transition = 'opacity 0.3s, transform 0.3s';

        const avatarBg = msg.is_ai ? 'bg-indigo-600 text-white' : (msg.is_agent ? 'bg-green-600 text-white' : 'bg-blue-600 text-white');
        let avatarContent = msg.is_ai ? '<i class="fas fa-robot"></i>' : (msg.is_agent ? '<i class="fas fa-headset"></i>' : (msg.sender_initial || userInitial));
        
        let labelHtml = '';
        if (msg.is_ai) labelHtml = '<span class="text-indigo-600">AI Assistant</span>';
        else if (msg.is_agent) labelHtml = `<span class="text-green-600">Agen: ${msg.sender_name}</span>`;
        else labelHtml = `<span class="text-blue-600">${msg.sender_name || 'User'}</span>`;

        const bubbleBg = msg.is_ai ? 'bg-indigo-50/50 border-indigo-100' : (msg.is_agent ? 'bg-green-50/50 border-green-100' : 'bg-gray-50 border-gray-200');

        wrapper.innerHTML = `
            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 shadow-sm ${avatarBg}">${avatarContent}</div>
            <div class="flex-1 min-w-0">
                <div class="text-[10px] text-gray-400 mb-1 font-semibold uppercase tracking-tight">
                    ${labelHtml}
                    <span class="text-gray-300 mx-1">•</span> ${msg.time}
                </div>
                <div class="rounded-2xl px-4 py-2.5 text-sm shadow-sm border ${bubbleBg} text-gray-800 inline-block max-w-[90%] whitespace-pre-wrap break-words">${msg.message}</div>
            </div>`;

        chatBox.appendChild(wrapper);
        requestAnimationFrame(() => {
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });
        scrollBottom();
    }

    // Poll
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
                data.messages.forEach(msg => {
                    if (!document.querySelector(`[data-msg-id="${msg.id}"]`)) {
                        renderAdminMsg(msg);
                    }
                });
                lastId = data.last_id;
            }

            if (data.status && data.status !== currentStatus) {
                currentStatus = data.status;
                // Reload for status changes (close/reopen affect action buttons)
                if (currentStatus === 'closed') {
                    location.reload();
                }
            }
        } catch (e) { /* ignore */ }
    }

    setInterval(poll, 3000);

    // Send via AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (sending) return;

        const text = input.value.trim();
        if (!text) return;

        sending = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

        try {
            const res = await fetch(sendUrl, {
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
                if (data.message && !document.querySelector(`[data-msg-id="${data.message.id}"]`)) {
                    renderAdminMsg(data.message);
                    lastId = Math.max(lastId, data.message.id);
                }
                input.value = '';
                
                // Hide waiting banner after first agent reply
                const banner = document.getElementById('admin-banner-waiting');
                if (banner) banner.classList.add('hidden');
            }
        } catch (e) { /* ignore */ }
        finally {
            sending = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = 'Kirim <i class="fas fa-arrow-right text-xs"></i>';
            input.focus();
        }
    });

    // Enter to send
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) poll();
    });
})();
</script>
@endpush
@endsection
