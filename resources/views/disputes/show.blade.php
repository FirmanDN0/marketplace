@extends('layouts.app')
@section('title', 'Dispute Resolution Center')
@section('content')
<div class="max-w-5xl mx-auto py-8">
    
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200/80 text-gray-600 hover:text-blue-600 shadow-sm transition-all duration-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-red-500/20">
            <i class="fas fa-gavel text-sm"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pusat Resolusi Sengketa</h1>
            <p class="text-sm text-gray-500 font-medium">Pesanan <a href="{{ route(auth()->user()->isAdmin() ? 'admin.orders.show' : (auth()->user()->isProvider() ? 'provider.orders.show' : 'customer.orders.show'), $dispute->order->id) }}" class="text-blue-600 hover:underline font-bold">#{{ $dispute->order->order_number }}</a></p>
        </div>
        <div class="ml-auto">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider
                {{ $dispute->status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $dispute->status === 'open' ? 'Dalam Peninjauan' : 'Selesai' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left sidebar: Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 p-6">
                <h3 class="text-lg font-extrabold text-gray-900 mb-4 border-b border-gray-100/80 pb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-400 text-sm"></i>Detail Sengketa</h3>
                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Dibuka Oleh</div>
                        <div class="text-sm font-medium text-gray-900">{{ optional($dispute->opener)->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Alasan</div>
                        <div class="text-sm font-bold text-red-600">{{ $dispute->reason }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Deskripsi</div>
                        <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{ $dispute->description }}
                        </div>
                    </div>
                    @if($dispute->status !== 'open')
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Resolusi (Oleh Admin)</div>
                        <div class="text-sm text-gray-700 bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                            {{ $dispute->resolution }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-blue-50/50 rounded-3xl p-6 border border-blue-100/80">
                <h3 class="text-sm font-bold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Panduan Negosiasi</h3>
                <p class="text-xs text-blue-800">
                    Gunakan area chat ini untuk mengunggah bukti tambahan berupa gambar (tangkapan layar chat, hasil kerja) atau bernegosiasi terkait pengembalian dana (refund parsial). Jika kesepakatan tercapai, Admin akan mengambil keputusan berdasarkan riwayat di sini.
                </p>
            </div>
        </div>

        {{-- Right side: Chat & Upload --}}
        <div class="lg:col-span-2 flex flex-col h-[65vh] bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
            {{-- Messages list --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50/50" id="dispute-msg-area">
                @forelse($dispute->messages as $msg)
                    @php $mine = $msg->user_id === auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%]">
                            @if(!$mine)
                                <div class="text-[11px] text-gray-500 mb-1 ml-1 font-medium">
                                    {{ optional($msg->user)->name }}
                                    @if(optional($msg->user)->isAdmin()) <span class="text-indigo-600 font-bold ml-1">(Admin)</span> @endif
                                </div>
                            @endif
                            <div class="{{ $mine ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm' : (optional($msg->user)->isAdmin() ? 'bg-indigo-50 text-indigo-900 border border-indigo-100 rounded-2xl rounded-tl-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-sm') }} px-4 py-3 shadow-sm">
                                
                                @if($msg->type === 'refund_proposal')
                                    <div class="mb-2 bg-amber-100/20 border border-amber-400/30 rounded-xl p-2 text-xs font-bold flex items-center gap-2 {{ $mine ? 'text-amber-100' : 'text-amber-700' }}">
                                        <i class="fas fa-handshake"></i>
                                        Mengajukan Pengembalian Dana: {{ $msg->refund_percentage }}%
                                    </div>
                                @endif

                                @if($msg->message)
                                    <div class="text-sm whitespace-pre-wrap">{{ $msg->message }}</div>
                                @endif

                                @if($msg->attachment_path)
                                    <div class="mt-3">
                                        @php
                                            $ext = strtolower(pathinfo($msg->attachment_path, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <a href="{{ route('attachments.dispute', $msg->id) }}" target="_blank" class="block overflow-hidden rounded-xl border border-black/10 hover:opacity-90 transition">
                                                <img src="{{ route('attachments.dispute', $msg->id) }}" class="max-h-48 w-auto object-cover">
                                            </a>
                                        @else
                                            <a href="{{ route('attachments.dispute', $msg->id) }}" target="_blank" class="{{ $mine ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-800' }} text-sm inline-flex items-center gap-2 font-semibold bg-black/5 px-3 py-2 rounded-lg">
                                                <i class="fas fa-file-download"></i> Unduh Lampiran
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <div class="text-[10px] {{ $mine ? 'text-blue-200' : 'text-gray-400' }} mt-1.5 text-right font-medium">{{ $msg->created_at->format('d M H:i') }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-comments text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada pesan. Silakan unggah bukti atau mulai diskusi.</p>
                    </div>
                @endforelse
            </div>

            {{-- Input Form --}}
            @if($dispute->status === 'open')
                <div class="border-t border-gray-100 p-4 bg-white" x-data="{ type: 'message' }">
                    <form action="{{ route('disputes.message', $dispute->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4 mb-3 px-1">
                            <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="type" value="message" x-model="type" class="text-blue-600 focus:ring-blue-500"> Pesan Biasa
                            </label>
                            <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 cursor-pointer">
                                <input type="radio" name="type" value="refund_proposal" x-model="type" class="text-amber-500 focus:ring-amber-500"> Ajukan Refund Parsial
                            </label>
                        </div>
                        
                        <div x-show="type === 'refund_proposal'" class="mb-3 flex items-center gap-3 bg-amber-50 p-3 rounded-xl border border-amber-100" style="display:none;">
                            <label class="text-xs font-bold text-amber-800">Persentase Pengembalian Dana (%):</label>
                            <input type="number" name="refund_percentage" min="1" max="100" placeholder="Contoh: 50" class="w-24 rounded-lg border border-amber-200 px-3 py-1.5 text-sm focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <textarea name="message" rows="2" placeholder="Tulis keterangan atau tanggapan Anda..." class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label title="Upload Bukti" class="cursor-pointer text-gray-500 hover:text-blue-600 transition bg-gray-50 hover:bg-blue-50 w-11 h-11 rounded-xl flex items-center justify-center border border-gray-200 hover:border-blue-200 shadow-sm">
                                    <i class="fas fa-paperclip text-lg"></i>
                                    <input type="file" name="attachment" class="hidden">
                                </label>
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 h-11 rounded-xl font-bold text-sm transition inline-flex items-center justify-center shadow-md">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const area = document.getElementById('dispute-msg-area');
    if(area) {
        area.scrollTop = area.scrollHeight;
    }
</script>
@endsection
