@extends('layouts.app')
@section('title', 'Conversation')
@section('content')
<div class="min-h-screen py-8">
<div class="max-w-4xl mx-auto px-4 sm:px-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('messages.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 shadow-sm transition-all duration-200 shrink-0" title="Kembali ke Pesan">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        @if(optional($other)->avatar)
            <img src="{{ Storage::url($other->avatar) }}" alt="{{ $other->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
        @else
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0">
                {{ strtoupper(substr(optional($other)->name ?? '?', 0, 1)) }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold text-gray-900">{{ optional($other)->name }}</h1>
            @if($conversation->service)
                <p class="text-sm text-gray-500">About: <a href="{{ route('services.show', $conversation->service->slug) }}" class="text-blue-600 hover:underline">{{ $conversation->service->title }}</a></p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <span id="rt-status" class="text-xs text-green-500 font-medium flex items-center gap-1 opacity-0 transition-opacity duration-300">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
            </span>
        </div>
    </div>

    {{-- Chat Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="height: 65vh;">
        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4" id="msg-area">
            @forelse($messages as $msg)
                @php $mine = $msg->sender_id === auth()->id(); @endphp
                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }} rt-msg" data-msg-id="{{ $msg->id }}">
                    <div class="max-w-[75%]">
                        @if(!$mine)
                            <div class="text-xs text-gray-400 mb-1 ml-1">{{ optional($msg->sender)->name }}</div>
                        @endif
                        <div class="{{ $mine ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md' }} px-4 py-3">
                            <div class="text-sm whitespace-pre-wrap break-words">{{ $msg->message_text }}</div>
                            @if($msg->attachment_path)
                                <div class="mt-2 pt-2 border-t {{ $mine ? 'border-blue-500' : 'border-gray-200' }}">
                                    <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank"
                                       class="{{ $mine ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-700' }} text-sm inline-flex items-center gap-1">
                                        <i class="fas fa-paperclip"></i> {{ $msg->attachment_name }}
                                    </a>
                                </div>
                            @endif
                            <div class="text-xs {{ $mine ? 'text-blue-200' : 'text-gray-400' }} mt-1 text-right">{{ $msg->created_at->format('d M H:i') }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400" id="empty-state">
                    <i class="fas fa-comments text-4xl mb-3"></i>
                    <p>No messages yet. Say hello!</p>
                </div>
            @endforelse
        </div>

        {{-- Send Form --}}
        <div class="border-t border-gray-100 p-4 bg-gray-50" x-data="{ fileName: '' }">
            <form id="msg-form" enctype="multipart/form-data" class="flex flex-col gap-2">
                @csrf
                <div x-show="fileName" style="display: none;" class="text-xs text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg w-max flex items-center gap-2">
                    <i class="fas fa-file-alt"></i> <span x-text="fileName" class="font-medium"></span>
                    <button type="button" @click="fileName = ''; $refs.fileInput.value = ''" class="text-red-500 hover:text-red-700 ml-2" title="Remove file"><i class="fas fa-times"></i></button>
                </div>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <textarea id="msg-input" name="message_text" rows="2" 
                                  placeholder="Type a message (Enter to send, Shift+Enter for new line)&#8230;"
                                  class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-2 pb-1">
                        <label title="Attach file" class="cursor-pointer text-gray-400 hover:text-blue-600 transition p-2">
                            <i class="fas fa-paperclip text-lg"></i>
                            <input type="file" name="attachment" class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                        </label>
                        <button type="submit" id="send-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
(() => {
    const convId        = {{ $conversation->id }};
    const pollUrl       = '/api/realtime/messages/' + convId + '/poll';
    const sendUrl       = '/api/realtime/messages/' + convId + '/send';
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').content;
    const msgArea       = document.getElementById('msg-area');
    const msgForm       = document.getElementById('msg-form');
    const msgInput      = document.getElementById('msg-input');
    const sendBtn       = document.getElementById('send-btn');
    const rtStatus      = document.getElementById('rt-status');
    const emptyState    = document.getElementById('empty-state');

    let lastId = {{ $messages->last()?->id ?? 0 }};
    let polling = true;
    let sending = false;

    // Scroll to bottom
    function scrollBottom() {
        msgArea.scrollTop = msgArea.scrollHeight;
    }
    scrollBottom();

    // Show live indicator
    setTimeout(() => { rtStatus.style.opacity = '1'; }, 500);

    // Render a single message bubble
    function renderMessage(msg) {
        if (emptyState) emptyState.remove();

        const wrapper = document.createElement('div');
        wrapper.className = `flex ${msg.mine ? 'justify-end' : 'justify-start'} rt-msg`;
        wrapper.dataset.msgId = msg.id;
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(10px)';
        wrapper.style.transition = 'opacity 0.3s, transform 0.3s';

        let attachmentHtml = '';
        if (msg.attachment_path) {
            const borderClass = msg.mine ? 'border-blue-500' : 'border-gray-200';
            const linkClass   = msg.mine ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-700';
            attachmentHtml = `
                <div class="mt-2 pt-2 border-t ${borderClass}">
                    <a href="${msg.attachment_path}" target="_blank" class="${linkClass} text-sm inline-flex items-center gap-1">
                        <i class="fas fa-paperclip"></i> ${msg.attachment_name || 'Download'}
                    </a>
                </div>`;
        }

        const bubbleClass = msg.mine
            ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md'
            : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md';
        const timeClass = msg.mine ? 'text-blue-200' : 'text-gray-400';

        wrapper.innerHTML = `
            <div class="max-w-[75%]">
                ${!msg.mine ? `<div class="text-xs text-gray-400 mb-1 ml-1">${msg.sender_name}</div>` : ''}
                <div class="${bubbleClass} px-4 py-3">
                    <div class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message_text || '')}</div>
                    ${attachmentHtml}
                    <div class="text-xs ${timeClass} mt-1 text-right">${msg.time}</div>
                </div>
            </div>`;

        msgArea.appendChild(wrapper);

        // Animate in
        requestAnimationFrame(() => {
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });

        scrollBottom();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
                data.messages.forEach(msg => {
                    if (!document.querySelector(`[data-msg-id="${msg.id}"]`)) {
                        renderMessage(msg);
                    }
                });
                lastId = data.last_id;
                // Trigger badge sync
                if (window.syncUiUnreadCounts) window.syncUiUnreadCounts(true);
            }
        } catch (e) { /* ignore */ }
    }

    // Poll every 3 seconds
    setInterval(poll, 3000);

    // Send message via AJAX
    msgForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (sending) return;

        const text = msgInput.value.trim();
        const fileInput = msgForm.querySelector('input[type="file"]');
        const hasFile = fileInput && fileInput.files.length > 0;

        if (!text && !hasFile) return;

        sending = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        const formData = new FormData();
        formData.append('message_text', text);
        if (hasFile) formData.append('attachment', fileInput.files[0]);

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData
            });

            if (res.ok) {
                const data = await res.json();
                if (data.message && !document.querySelector(`[data-msg-id="${data.message.id}"]`)) {
                    renderMessage(data.message);
                    lastId = Math.max(lastId, data.message.id);
                }
                msgInput.value = '';
                if (fileInput) fileInput.value = '';
                // Reset AlpineJS fileName
                const alpineRoot = msgForm.closest('[x-data]');
                if (alpineRoot && alpineRoot.__x) {
                    alpineRoot.__x.$data.fileName = '';
                }
            }
        } catch (e) { /* ignore */ }
        finally {
            sending = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
            msgInput.focus();
        }
    });

    // Enter to send, Shift+Enter for newline
    msgInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            msgForm.dispatchEvent(new Event('submit'));
        }
    });

    // Pause polling when tab is not visible
    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) poll();
    });
})();
</script>
@endpush
@endsection
