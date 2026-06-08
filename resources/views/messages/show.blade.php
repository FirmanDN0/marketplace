@extends('layouts.app')
@section('title', 'Percakapan')
@section('content')
<div id="messages-root" class="min-h-screen py-8" x-data="{ showLightbox: false, lightboxUrl: '', lightboxName: '' }">
<div class="max-w-4xl mx-auto px-4 sm:px-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('messages.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-white border border-gray-100/80 text-gray-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 shadow-sm transition-all duration-200 shrink-0" title="Kembali ke Pesan">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        @if(optional($other)->avatar)
            <img src="{{ Storage::url($other->avatar) }}" alt="{{ $other->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
        @else
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-md shadow-blue-500/20">
                {{ strtoupper(substr(optional($other)->name ?? '?', 0, 1)) }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h1 class="flex items-center text-lg font-extrabold text-gray-900 tracking-tight">
                {{ optional($other)->name }}
                <x-verified-badge :user="$other" size="text-sm" />
            </h1>
            @if($conversation->service)
                <p class="text-sm text-gray-500 font-medium">Tentang: <a href="{{ route('services.show', $conversation->service->slug) }}" class="text-blue-600 hover:underline">{{ $conversation->service->title }}</a></p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <!-- Realtime indicator removed as per request -->
        </div>
    </div>

    {{-- Chat Box --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden flex flex-col" style="height: 65vh;">
        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4" id="msg-area">
            @forelse($messages as $msg)
                @php $mine = $msg->sender_id === auth()->id(); @endphp
                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }} rt-msg" 
                     data-msg-id="{{ $msg->id }}"
                     data-msg-text="{{ $msg->message_text }}"
                     data-msg-mine="{{ $mine ? 'true' : 'false' }}"
                     data-msg-attachment-url="{{ $msg->attachment_path ? Storage::url($msg->attachment_path) : '' }}"
                     data-msg-sender-name="{{ $mine ? 'You' : optional($msg->sender)->name }}"
                     data-msg-time="{{ $msg->created_at->format('d M H:i') }}">
                    <div class="max-w-[75%]">
                        @if(!$mine)
                            <div class="flex items-center gap-1 text-xs text-gray-400 mb-1 ml-1">
                                {{ optional($msg->sender)->name }}
                                <x-verified-badge :user="$msg->sender" size="text-[10px]" />
                            </div>
                        @endif
                        <div class="{{ $mine ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md' }} px-4 py-3">
                            @if($msg->replyTo)
                                <div class="mb-2 p-2.5 rounded-lg bg-black/5 border-l-4 {{ $mine ? 'border-blue-300' : 'border-gray-400' }} text-sm cursor-pointer hover:bg-black/10 transition" onclick="const el = document.querySelector('[data-msg-id=\'{{ $msg->replyTo->id }}\']'); if(el){el.scrollIntoView({behavior: 'smooth', block: 'center'}); el.classList.add('pulse-glow'); setTimeout(()=>el.classList.remove('pulse-glow'), 2000);}">
                                    <div class="font-bold {{ $mine ? 'text-blue-100' : 'text-gray-700' }} text-[11px] mb-0.5">{{ optional($msg->replyTo->sender)->name ?? 'Unknown' }}</div>
                                    <div class="text-xs truncate {{ $mine ? 'text-blue-50' : 'text-gray-600' }}">{{ $msg->replyTo->message_text ?: '[Lampiran: ' . ($msg->replyTo->attachment_name ?? 'File') . ']' }}</div>
                                </div>
                            @endif
                            <div class="msg-text text-sm whitespace-pre-wrap break-words">{{ $msg->message_text }}</div>
                            @if($msg->attachment_path)
                                @php
                                    $ext = strtolower(pathinfo($msg->attachment_path, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']);
                                @endphp
                                @if($isImage)
                                    <div class="mt-2.5">
                                        <button type="button" onclick="window.openLightbox('{{ Storage::url($msg->attachment_path) }}', '{{ addslashes($msg->attachment_name) }}')" 
                                                class="block text-left overflow-hidden rounded-xl border border-black/5 hover:opacity-90 transition cursor-pointer">
                                            <img src="{{ Storage::url($msg->attachment_path) }}" alt="{{ $msg->attachment_name }}" class="max-h-56 w-auto object-cover rounded-xl shadow-sm">
                                        </button>
                                    </div>
                                @else
                                    <div class="mt-2 pt-2 border-t {{ $mine ? 'border-blue-500' : 'border-gray-200' }}">
                                        <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank"
                                           class="{{ $mine ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-700' }} text-sm inline-flex items-center gap-1 font-semibold">
                                            <i class="fas fa-paperclip"></i> {{ $msg->attachment_name }}
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if($msg->customOffer)
                                <div class="mt-2 mb-2 bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
                                    <div class="bg-orange-50 px-4 py-2 border-b border-orange-100 flex items-center gap-2">
                                        <i class="fas fa-file-invoice-dollar text-orange-600"></i>
                                        <span class="font-bold text-orange-900 text-xs uppercase tracking-wider">Penawaran Kustom</span>
                                        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full {{ $msg->customOffer->status === 'pending' ? 'bg-amber-200 text-amber-800' : ($msg->customOffer->status === 'accepted' || $msg->customOffer->status === 'paid' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800') }}">
                                            {{ strtoupper($msg->customOffer->status) }}
                                        </span>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <div>
                                            <h4 class="font-bold text-gray-900 leading-tight">{{ $msg->customOffer->title }}</h4>
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-3">{{ $msg->customOffer->description }}</p>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm font-semibold text-gray-800 bg-gray-50 rounded-lg p-2 border border-gray-100">
                                            <div class="flex-1 border-r border-gray-200"><i class="fas fa-tag text-gray-400 mr-1.5"></i> Rp {{ number_format($msg->customOffer->price, 0, ',', '.') }}</div>
                                            <div class="flex-1"><i class="far fa-clock text-gray-400 mr-1.5"></i> {{ $msg->customOffer->delivery_days }} Hari</div>
                                        </div>
                                        @if(!$mine && $msg->customOffer->status === 'pending')
                                            <form action="{{ route('customer.custom-offer.pay', $msg->customOffer->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full mt-2 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 rounded-lg text-sm transition shadow-sm">
                                                    Bayar Penawaran
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="text-[10px] {{ $mine ? 'text-blue-200' : 'text-gray-400' }} mt-1 text-right flex items-center justify-end gap-1">
                                <span>{{ $msg->created_at->format('d M H:i') }}</span>
                                @if($mine)
                                    @if($msg->read_at)
                                        <i class="fas fa-check-double text-blue-300" title="Dibaca pada {{ $msg->read_at->format('H:i') }}"></i>
                                    @else
                                        <i class="fas fa-check text-blue-200/50" title="Terkirim"></i>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400" id="empty-state">
                    <i class="fas fa-comments text-4xl mb-3"></i>
                    <p class="font-medium">Belum ada pesan. Sapa duluan!</p>
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 p-4 bg-gray-50" x-data="{ fileName: '', isImage: false, previewUrl: '', replyToId: null, replyToName: '', replyToText: '' }">
            <form id="msg-form" enctype="multipart/form-data" class="flex flex-col gap-2">
                @csrf
                <div x-show="fileName" style="display: none;" class="mb-3">
                    {{-- Image preview thumbnail --}}
                    <template x-if="isImage && previewUrl">
                        <div class="relative inline-block">
                            <div class="overflow-hidden rounded-2xl border border-gray-200/80 shadow-sm bg-white p-1">
                                <img :src="previewUrl" class="h-20 w-auto object-cover rounded-xl max-w-[200px]">
                            </div>
                            <button type="button" 
                                    @click="if (previewUrl) URL.revokeObjectURL(previewUrl); fileName = ''; isImage = false; previewUrl = ''; $refs.fileInput.value = ''"
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md transition transform hover:scale-110 cursor-pointer text-xs" 
                                    title="Hapus file">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                    {{-- Non-image file badge --}}
                    <template x-if="!isImage">
                        <div class="text-xs text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg w-max flex items-center gap-2 border border-blue-100/50 shadow-sm">
                            <i class="fas fa-file-alt"></i> <span x-text="fileName" class="font-medium"></span>
                            <button type="button" 
                                    @click="fileName = ''; isImage = false; previewUrl = ''; $refs.fileInput.value = ''"
                                    class="text-red-500 hover:text-red-700 ml-2 transition cursor-pointer" 
                                    title="Hapus file">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                </div>
                
                {{-- Reply To Block --}}
                <div x-show="replyToId" style="display: none;" class="mb-2 bg-white rounded-xl p-3 relative border-l-4 border-blue-500 text-left shadow-sm flex items-start gap-2">
                    <div class="mt-1 text-blue-500"><i class="fas fa-reply text-lg"></i></div>
                    <div class="flex-1 overflow-hidden">
                        <div class="text-xs font-bold text-blue-600 mb-0.5" x-text="replyToName"></div>
                        <div class="text-xs text-gray-600 truncate" x-text="replyToText"></div>
                    </div>
                    <button type="button" @click="replyToId = null; replyToName = ''; replyToText = ''" class="text-gray-400 hover:text-red-500 transition cursor-pointer p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <textarea id="msg-input" name="message_text" rows="2" 
                                  placeholder="Ketik pesan (Enter untuk kirim, Shift+Enter baris baru)&#8230;"
                                  class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-2 pb-1">
                        @if(auth()->user()->isProvider())
                        <button type="button" id="ai-reply-btn" onclick="generateAIReply()" title="Buatkan Balasan dengan AI" class="cursor-pointer text-gray-400 hover:text-blue-600 transition p-2">
                            <i class="fas fa-magic text-lg"></i>
                        </button>
                        <button type="button" onclick="document.getElementById('custom-offer-modal').style.display='flex'" title="Buat Penawaran Kustom" class="cursor-pointer text-gray-400 hover:text-orange-500 transition p-2">
                            <i class="fas fa-file-invoice-dollar text-lg"></i>
                        </button>
                        @endif
                        <label title="Attach file" class="cursor-pointer text-gray-400 hover:text-blue-600 transition p-2">
                            <i class="fas fa-paperclip text-lg"></i>
                            <input type="file" name="attachment" class="hidden" x-ref="fileInput" 
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           fileName = file.name;
                                           isImage = file.type.startsWith('image/');
                                           if (isImage) {
                                               previewUrl = URL.createObjectURL(file);
                                           } else {
                                               previewUrl = '';
                                           }
                                       } else {
                                           fileName = '';
                                           isImage = false;
                                           previewUrl = '';
                                       }
                                   ">
                        </label>
                        <button type="submit" id="send-btn" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all inline-flex items-center gap-2 shadow-lg shadow-blue-500/15">
                            <i class="fas fa-paper-plane"></i> Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Custom Offer Modal --}}
    @if(auth()->user()->isProvider())
    <div id="custom-offer-modal" class="fixed inset-0 z-[60] items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
         <div class="bg-white rounded-3xl shadow-xl max-w-md w-full overflow-hidden relative">
             <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                 <h3 class="font-bold text-gray-900"><i class="fas fa-file-invoice-dollar text-orange-500 mr-2"></i>Buat Penawaran Kustom</h3>
                 <button type="button" onclick="document.getElementById('custom-offer-modal').style.display='none'" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
             </div>
             <div class="p-5">
                 <form id="custom-offer-form" class="space-y-4">
                     @csrf
                     <div>
                         <label class="block text-xs font-bold text-gray-700 mb-1">Judul Penawaran <span class="text-red-500">*</span></label>
                         <input type="text" name="title" id="offer-title" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Misal: Desain Logo Premium Tanpa Revisi">
                     </div>
                     <div>
                         <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                         <textarea name="description" id="offer-desc" required rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none" placeholder="Jelaskan spesifikasi pekerjaan secara detail..."></textarea>
                     </div>
                     <div class="grid grid-cols-2 gap-4">
                         <div>
                             <label class="block text-xs font-bold text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                             <input type="number" name="price" id="offer-price" required min="1000" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="150000">
                             <p class="text-[10px] text-gray-400 mt-1 italic">* Anda akan menerima 100% dari harga ini</p>
                         </div>
                         <div>
                             <label class="block text-xs font-bold text-gray-700 mb-1">Durasi (Hari) <span class="text-red-500">*</span></label>
                             <input type="number" name="delivery_days" id="offer-days" required min="1" max="90" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="3">
                         </div>
                     </div>
                     <button type="submit" id="offer-submit-btn" class="w-full mt-2 bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl text-sm transition flex justify-center items-center gap-2 shadow-sm">
                         <i class="fas fa-paper-plane"></i> Kirim Penawaran
                     </button>
                 </form>
             </div>
         </div>
    </div>
    @endif

    {{-- Lightbox Modal --}}
    <div x-show="showLightbox" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
         @click.away="showLightbox = false"
         @keydown.escape.window="showLightbox = false"
         style="display: none;">
        
        <div class="relative max-w-4xl w-full flex flex-col items-center"
             x-transition:enter="transition ease-out duration-300 transform scale-95"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform scale-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
             
            {{-- Close button --}}
            <button @click="showLightbox = false" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-2xl transition cursor-pointer" title="Tutup">
                <i class="fas fa-times"></i>
            </button>
            
            {{-- Image --}}
            <div class="bg-white rounded-3xl p-3 shadow-2xl overflow-hidden max-h-[80vh]">
                <img :src="lightboxUrl" :alt="lightboxName" class="max-h-[70vh] w-auto object-contain rounded-2xl mx-auto">
            </div>
            
            {{-- Caption / Filename --}}
            <div class="mt-4 text-center">
                <p class="text-white text-sm font-semibold truncate max-w-md" x-text="lightboxName"></p>
                <a :href="lightboxUrl" download :title="lightboxName" class="inline-flex items-center gap-1.5 text-xs text-blue-400 hover:text-blue-300 font-bold uppercase tracking-wider mt-2.5 transition">
                    <i class="fas fa-download"></i> Unduh Gambar
                </a>
            </div>
        </div>
    </div>

    {{-- Context Menu --}}
    <div id="chat-context-menu" class="fixed z-50 bg-white/95 backdrop-blur-md border border-gray-200/50 rounded-2xl shadow-xl py-2 min-w-[180px] hidden transition-all duration-100 scale-95 opacity-0 origin-top-left">
        <button onclick="contextAction('copy')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2 font-medium cursor-pointer">
            <i class="far fa-copy text-gray-400"></i> Salin Teks
        </button>
        <button id="context-reply" onclick="contextAction('reply')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2 font-medium cursor-pointer">
            <i class="fas fa-reply text-gray-400"></i> Balas Pesan
        </button>
        <button id="context-download" onclick="contextAction('download')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2 font-medium cursor-pointer">
            <i class="fas fa-download text-gray-400"></i> Unduh Lampiran
        </button>
        <button id="context-info" onclick="contextAction('info')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2 font-medium cursor-pointer">
            <i class="fas fa-info-circle text-gray-400"></i> Info Detail
        </button>
        <div id="context-delete-divider" class="border-t border-gray-100 my-1"></div>
        <button id="context-delete" onclick="contextAction('delete')" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2 font-medium cursor-pointer">
            <i class="far fa-trash-alt text-red-400"></i> Hapus Pesan
        </button>
    </div>

</div>
</div>

@push('scripts')
<script type="module">
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

    // Show live indicator if exists
    if (rtStatus) {
        setTimeout(() => { rtStatus.style.opacity = '1'; }, 500);
    }

    // Render a single message bubble
    function renderMessage(msg) {
        if (emptyState) emptyState.remove();

        const wrapper = document.createElement('div');
        wrapper.className = `flex ${msg.mine ? 'justify-end' : 'justify-start'} rt-msg`;
        wrapper.dataset.msgId = msg.id;
        wrapper.dataset.msgText = msg.message_text || '';
        wrapper.dataset.msgMine = msg.mine ? 'true' : 'false';
        wrapper.dataset.msgAttachmentUrl = msg.attachment_path ? (msg.attachment_path.startsWith('/') ? msg.attachment_path : `/storage/${msg.attachment_path}`) : '';
        wrapper.dataset.msgSenderName = msg.mine ? 'You' : msg.sender_name;
        wrapper.dataset.msgTime = msg.time;
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(10px)';
        wrapper.style.transition = 'opacity 0.3s, transform 0.3s';

        let attachmentHtml = '';
        if (msg.attachment_path) {
            const borderClass = msg.mine ? 'border-blue-500' : 'border-gray-200';
            const linkClass   = msg.mine ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-700';
            
            const path = msg.attachment_path;
            const ext = path.split('.').pop().toLowerCase();
            const isImg = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext);

            if (isImg) {
                attachmentHtml = `
                    <div class="mt-2.5">
                        <button type="button" onclick="window.openLightbox('${path}', '${escapeHtml(msg.attachment_name || 'Image')}')" 
                                class="block text-left overflow-hidden rounded-xl border border-black/5 hover:opacity-90 transition cursor-pointer">
                            <img src="${path}" alt="${msg.attachment_name || 'Image'}" class="max-h-56 w-auto object-cover rounded-xl shadow-sm">
                        </button>
                    </div>`;
            } else {
                attachmentHtml = `
                    <div class="mt-2 pt-2 border-t ${borderClass}">
                        <a href="${path}" target="_blank" class="${linkClass} text-sm inline-flex items-center gap-1 font-semibold">
                            <i class="fas fa-paperclip"></i> ${msg.attachment_name || 'Download'}
                        </a>
                    </div>`;
            }
        }

        let customOfferHtml = '';
        if (msg.custom_offer) {
            const statusClass = msg.custom_offer.status === 'pending' ? 'bg-amber-200 text-amber-800' 
                              : (msg.custom_offer.status === 'accepted' || msg.custom_offer.status === 'paid' ? 'bg-green-200 text-green-800' 
                              : 'bg-red-200 text-red-800');
                              
            let payButtonHtml = '';
            if (!msg.mine && msg.custom_offer.status === 'pending') {
                payButtonHtml = `
                    <form action="/customer/custom-offers/${msg.custom_offer.id}/pay" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="w-full mt-2 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 rounded-lg text-sm transition shadow-sm">
                            Bayar Penawaran
                        </button>
                    </form>`;
            }

            customOfferHtml = `
                <div class="mt-2 mb-2 bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden text-gray-800">
                    <div class="bg-orange-50 px-4 py-2 border-b border-orange-100 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-orange-600"></i>
                        <span class="font-bold text-orange-900 text-xs uppercase tracking-wider">Penawaran Kustom</span>
                        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full ${statusClass}">
                            ${msg.custom_offer.status.toUpperCase()}
                        </span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <h4 class="font-bold text-gray-900 leading-tight">${escapeHtml(msg.custom_offer.title)}</h4>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-3">${escapeHtml(msg.custom_offer.description)}</p>
                        </div>
                        <div class="flex items-center gap-4 text-sm font-semibold text-gray-800 bg-gray-50 rounded-lg p-2 border border-gray-100">
                            <div class="flex-1 border-r border-gray-200"><i class="fas fa-tag text-gray-400 mr-1.5"></i> Rp ${msg.custom_offer.price}</div>
                            <div class="flex-1"><i class="far fa-clock text-gray-400 mr-1.5"></i> ${msg.custom_offer.delivery_days} Hari</div>
                        </div>
                        ${payButtonHtml}
                    </div>
                </div>`;
        }

        const bubbleClass = msg.mine
            ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md'
            : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md';
        const timeClass = msg.mine ? 'text-blue-200' : 'text-gray-400';

        let readStatusHtml = '';
        if (msg.mine) {
            if (msg.read_at) {
                readStatusHtml = '<i class="fas fa-check-double text-blue-300" title="Dibaca"></i>';
            } else {
                readStatusHtml = '<i class="fas fa-check text-blue-200/50" title="Terkirim"></i>';
            }
        }

        wrapper.innerHTML = `
            <div class="max-w-[75%]">
                ${!msg.mine ? `<div class="text-xs text-gray-400 mb-1 ml-1">${msg.sender_name}</div>` : ''}
                <div class="${bubbleClass} px-4 py-3">
                    ${msg.reply_to ? `
                        <div class="mb-2 p-2.5 rounded-lg bg-black/5 border-l-4 ${msg.mine ? 'border-blue-300' : 'border-gray-400'} text-sm cursor-pointer hover:bg-black/10 transition" onclick="const el = document.querySelector('[data-msg-id=\\'${msg.reply_to.id}\\']'); if(el){el.scrollIntoView({behavior: 'smooth', block: 'center'}); el.classList.add('pulse-glow'); setTimeout(()=>el.classList.remove('pulse-glow'), 2000);}">
                            <div class="font-bold ${msg.mine ? 'text-blue-100' : 'text-gray-700'} text-[11px] mb-0.5">${escapeHtml(msg.reply_to.sender_name)}</div>
                            <div class="text-xs truncate ${msg.mine ? 'text-blue-50' : 'text-gray-600'}">${escapeHtml(msg.reply_to.message_text || ('[Lampiran: ' + (msg.reply_to.attachment_name || 'File') + ']'))}</div>
                        </div>
                    ` : ''}
                    <div class="msg-text text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message_text || '')}</div>
                    ${attachmentHtml}
                    ${customOfferHtml}
                    <div class="text-[10px] ${timeClass} mt-1 text-right flex items-center justify-end gap-1">
                        <span>${msg.time}</span>
                        ${readStatusHtml}
                    </div>
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

    // Global lightbox opener compatible with Blade & AJAX
    window.openLightbox = function(url, name) {
        const root = document.getElementById('messages-root');
        if (root && window.Alpine) {
            const data = window.Alpine.$data(root);
            data.lightboxUrl = url;
            data.lightboxName = name;
            data.showLightbox = true;
        } else {
            window.open(url, '_blank');
        }
    };

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
                
                // If we received a message from the other person, they have likely read our messages
                // Let's optimistically update all our sent single checks to double checks!
                document.querySelectorAll('.fa-check.text-blue-200\\/50').forEach(icon => {
                    icon.classList.remove('fa-check', 'text-blue-200/50');
                    icon.classList.add('fa-check-double', 'text-blue-300');
                    icon.setAttribute('title', 'Dibaca');
                });
            }
        } catch (e) { /* ignore */ }
    }

    // Initialize WebSocket listener if Echo is available, otherwise fallback to polling
    if (window.Echo) {
        console.log('Echo is available. Listening to conversation.' + convId);
        window.Echo.private('conversation.' + convId)
            .listen('MessageSent', (e) => {
                const isMine = e.message.sender_id === {{ auth()->id() }};
                e.message.mine = isMine;
                if (!document.querySelector(`[data-msg-id="${e.message.id}"]`)) {
                    renderMessage(e.message);
                    lastId = Math.max(lastId, e.message.id);
                    if (window.syncUiUnreadCounts) window.syncUiUnreadCounts(true);
                    
                    // Mark as read via API
                    fetch(pollUrl + '?after=' + lastId, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });

                    if (!isMine) {
                        // If we received a message, we can assume they opened the chat and read ours
                        document.querySelectorAll('.fa-check.text-blue-200\\/50').forEach(icon => {
                            icon.classList.remove('fa-check', 'text-blue-200/50');
                            icon.classList.add('fa-check-double', 'text-blue-300');
                            icon.setAttribute('title', 'Dibaca');
                        });
                    }
                }
            })
            .listen('MessageRead', (e) => {
                document.querySelectorAll('.fa-check.text-blue-200\\/50').forEach(icon => {
                    icon.classList.remove('fa-check', 'text-blue-200/50');
                    icon.classList.add('fa-check-double', 'text-blue-300');
                    icon.setAttribute('title', 'Dibaca');
                });
            });
    } else {
        console.log('Echo is not available. Falling back to AJAX polling.');
        setInterval(poll, 3000);
    }

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
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

        const formData = new FormData();
        formData.append('message_text', text);
        if (hasFile) formData.append('attachment', fileInput.files[0]);

        const alpineRoot = msgForm.closest('[x-data]');
        if (alpineRoot && window.Alpine) {
            const data = window.Alpine.$data(alpineRoot);
            if (data.replyToId) formData.append('reply_to_id', data.replyToId);
        }

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
                // Reset AlpineJS attachment state
                const alpineRoot = msgForm.closest('[x-data]');
                if (alpineRoot && window.Alpine) {
                    const data = window.Alpine.$data(alpineRoot);
                    if (data.previewUrl) {
                        URL.revokeObjectURL(data.previewUrl);
                    }
                    data.fileName = '';
                    data.isImage = false;
                    data.previewUrl = '';
                    data.replyToId = null;
                    data.replyToName = '';
                    data.replyToText = '';
                }
            }
        } catch (e) { /* ignore */ }
        finally {
            sending = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim';
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

    // Custom Offer Form Logic
    const offerForm = document.getElementById('custom-offer-form');
    if (offerForm) {
        offerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('offer-submit-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            btn.disabled = true;

            const fd = new FormData(offerForm);
            try {
                const res = await fetch('{{ route("realtime.messages.custom-offer", $conversation->id) }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                
                const data = await res.json();
                if (res.ok && data.success) {
                    document.getElementById('custom-offer-modal').style.display = 'none';
                    offerForm.reset();
                    appendMessage(data.message);
                } else {
                    alert(data.message || 'Gagal mengirim penawaran.');
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    // Pause polling when tab is not visible
    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) poll();
    });

    // Context Menu Logic
    const contextMenu = document.getElementById('chat-context-menu');
    let activeMsgData = null;

    // Show custom context menu
    msgArea.addEventListener('contextmenu', (e) => {
        const msgWrapper = e.target.closest('.rt-msg');
        if (!msgWrapper) return;
        
        e.preventDefault();
        
        activeMsgData = {
            id: msgWrapper.dataset.msgId,
            text: msgWrapper.dataset.msgText || '',
            mine: msgWrapper.dataset.msgMine === 'true',
            attachmentUrl: msgWrapper.dataset.msgAttachmentUrl || '',
            senderName: msgWrapper.dataset.msgSenderName || '',
            time: msgWrapper.dataset.msgTime || '',
            element: msgWrapper
        };

        const deleteBtn = document.getElementById('context-delete');
        const deleteDiv = document.getElementById('context-delete-divider');
        const downloadBtn = document.getElementById('context-download');
        const replyBtn = document.getElementById('context-reply');

        // Delete only own messages
        if (activeMsgData.mine) {
            deleteBtn.style.display = 'flex';
            if (deleteDiv) deleteDiv.style.display = 'block';
        } else {
            deleteBtn.style.display = 'none';
            if (deleteDiv) deleteDiv.style.display = 'none';
        }

        // Download only if there is an attachment
        if (activeMsgData.attachmentUrl) {
            downloadBtn.style.display = 'flex';
        } else {
            downloadBtn.style.display = 'none';
        }

        // Hide reply option if message text and attachment are both empty
        if (!activeMsgData.text && !activeMsgData.attachmentUrl) {
            replyBtn.style.display = 'none';
        } else {
            replyBtn.style.display = 'flex';
        }

        // Positioning context menu
        contextMenu.style.left = `${e.clientX}px`;
        contextMenu.style.top = `${e.clientY}px`;
        contextMenu.classList.remove('hidden');
        
        setTimeout(() => {
            contextMenu.style.transform = 'scale(1)';
            contextMenu.style.opacity = '1';
        }, 10);
    });

    // Dismiss context menu
    document.addEventListener('click', () => {
        hideContextMenu();
    });

    msgArea.addEventListener('scroll', () => {
        hideContextMenu();
    });

    function hideContextMenu() {
        if (contextMenu && !contextMenu.classList.contains('hidden')) {
            contextMenu.style.transform = 'scale(0.95)';
            contextMenu.style.opacity = '0';
            setTimeout(() => {
                contextMenu.classList.add('hidden');
            }, 100);
        }
    }

    // Context actions implementation
    window.contextAction = async function(action) {
        if (!activeMsgData) return;
        hideContextMenu();

        if (action === 'copy') {
            const copyContent = activeMsgData.text || activeMsgData.attachmentUrl;
            if (!copyContent) return;
            try {
                await navigator.clipboard.writeText(copyContent);
                showToast('Pesan disalin ke clipboard!', 'success');
            } catch (err) {
                showToast('Gagal menyalin pesan.', 'error');
            }
        } 
        else if (action === 'reply') {
            const author = activeMsgData.senderName;
            const content = activeMsgData.text ? activeMsgData.text.replace(/\n/g, ' ') : '[Lampiran: ' + (activeMsgData.attachmentName || 'File') + ']';
            
            const alpineRoot = msgForm.closest('[x-data]');
            if (alpineRoot && window.Alpine) {
                const data = window.Alpine.$data(alpineRoot);
                data.replyToId = activeMsgData.id;
                data.replyToName = author;
                data.replyToText = content;
            }
            msgInput.focus();
        } 
        else if (action === 'download') {
            if (activeMsgData.attachmentUrl) {
                const link = document.createElement('a');
                link.href = activeMsgData.attachmentUrl;
                link.download = '';
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast('Mengunduh lampiran...', 'info');
            }
        } 
        else if (action === 'info') {
            const infoText = `Dikirim oleh ${activeMsgData.senderName} pada ${activeMsgData.time}`;
            showToast(infoText, 'info');
        } 
        else if (action === 'delete') {
            Swal.fire({
                title: 'Hapus Pesan?',
                text: 'Apakah Anda yakin ingin menghapus pesan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[1.5rem] shadow-2xl font-sans border border-gray-100',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-6 rounded-xl transition-all mr-3',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2.5 px-6 rounded-xl transition-all'
                },
                buttonsStyling: false
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(`/messages/${activeMsgData.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) {
                            const el = activeMsgData.element;
                            el.style.transition = 'all 0.3s ease';
                            el.style.opacity = '0';
                            el.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                el.remove();
                            }, 300);
                            showToast('Pesan berhasil dihapus.', 'success');
                        } else {
                            showToast('Gagal menghapus pesan.', 'error');
                        }
                    } catch (e) {
                        showToast('Gagal menghapus pesan.', 'error');
                    }
                }
            });
        }
    };

    // Micro Toast Notification helper
    function showToast(message, type = 'info') {
        let toast = document.getElementById('chat-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'chat-toast';
            toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-full text-xs font-semibold shadow-lg backdrop-blur-md transition-all duration-300 transform translate-y-4 opacity-0 pointer-events-none flex items-center gap-2';
            document.body.appendChild(toast);
        }
        
        // Reset dynamic classes
        toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-full text-xs font-semibold shadow-lg backdrop-blur-md transition-all duration-300 transform translate-y-4 opacity-0 pointer-events-none flex items-center gap-2';
        
        if (type === 'success') {
            toast.classList.add('bg-emerald-500/90', 'text-white', 'border', 'border-emerald-400/20');
            toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        } else if (type === 'error') {
            toast.classList.add('bg-red-500/90', 'text-white', 'border', 'border-red-400/20');
            toast.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        } else {
            toast.classList.add('bg-blue-600/90', 'text-white', 'border', 'border-blue-500/20');
            toast.innerHTML = '<i class="fas fa-info-circle"></i> ' + message;
        }

        // Show toast
        toast.style.transform = 'translateY(0) translateX(-50%)';
        toast.style.opacity = '1';

        // Auto dismiss after 2.5s
        setTimeout(() => {
            if (toast) {
                toast.style.transform = 'translateY(4px) translateX(-50%)';
                toast.style.opacity = '0';
            }
        }, 2500);
    }
})();

function generateAIReply() {
    const messageEls = document.querySelectorAll('.msg-text');
    let context = "";
    // Grab the last 5 messages for better context
    const recentMessages = Array.from(messageEls).slice(-5);
    recentMessages.forEach(el => {
        if(el.innerText) context += "- " + el.innerText.trim() + "\n";
    });

    if (!context) {
        Swal.fire({
            icon: 'info',
            title: 'Oops...',
            text: 'Belum ada pesan yang bisa dibalas.',
            confirmButtonColor: '#4f46e5'
        });
        return;
    }

    const btn = document.getElementById('ai-reply-btn');
    const icon = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-lg text-blue-600"></i>';
    btn.disabled = true;

    fetch('{{ route("ai.reply-assistant") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ context: context.trim(), type: 'chat' })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success && data.reply) {
            document.getElementById('msg-input').value = data.reply;
            document.getElementById('msg-input').dispatchEvent(new Event('input'));
            Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: 'Balasan telah disiapkan oleh AI. Silakan periksa kembali sebelum mengirim.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Gagal menghasilkan balasan AI.',
                confirmButtonColor: '#4f46e5'
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Terjadi kesalahan sistem saat menghubungi AI.',
            confirmButtonColor: '#4f46e5'
        });
    })
    .finally(() => {
        btn.innerHTML = icon;
        btn.disabled = false;
    });
}
</script>
@endpush
@endsection
