@extends('layouts.app')
@section('title', 'Customer Service')
@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Hero Header --}}
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 sm:p-8 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <div class="w-11 h-11 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-headset text-lg"></i>
                    </div>
                    Pusat Bantuan
                </h1>
                <p class="text-blue-100 text-sm mt-2 ml-14">AI kami siap membantu Anda 24/7. Butuh bantuan manusia? Tinggal minta saja.</p>
            </div>
            <a href="{{ route('customer-service.start') }}" class="bg-white text-blue-700 hover:bg-blue-50 px-5 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center gap-2 self-start sm:self-auto shadow-lg shadow-blue-900/20">
                <i class="fas fa-plus"></i> Percakapan Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($conversations->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-500 flex items-center justify-center mx-auto mb-5 text-3xl">
                <i class="fas fa-headset"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Belum ada percakapan</h3>
            <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">Mulai percakapan baru untuk mendapatkan bantuan instan dari AI Customer Service kami.</p>
            <a href="{{ route('customer-service.start') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fas fa-rocket"></i> Mulai Sekarang
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Percakapan Saya</h3>
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $conversations->total() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($conversations as $conv)
                @php
                    $statusConfig = match($conv->status) {
                        'ai'     => ['color' => 'bg-indigo-100 text-indigo-700', 'icon' => 'fa-robot', 'label' => 'AI', 'dot' => 'bg-indigo-500'],
                        'human'  => ['color' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-headset', 'label' => 'Menunggu Agen', 'dot' => 'bg-amber-500'],
                        'closed' => ['color' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-check-circle', 'label' => 'Selesai', 'dot' => 'bg-gray-400'],
                        default  => ['color' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-circle', 'label' => $conv->status, 'dot' => 'bg-gray-400'],
                    };
                    $avatarBg = match($conv->status) {
                        'ai'     => 'bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600',
                        'human'  => 'bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600',
                        'closed' => 'bg-gray-100 text-gray-400',
                        default  => 'bg-gray-100 text-gray-400',
                    };
                @endphp
                <a href="{{ route('customer-service.show', $conv->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-blue-50/40 transition group">
                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl {{ $avatarBg }} flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $statusConfig['icon'] }}"></i>
                        </div>
                        @if($conv->status !== 'closed')
                        <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 {{ $statusConfig['dot'] }} rounded-full border-2 border-white"></div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 group-hover:text-blue-600 transition text-sm">{{ $conv->subject ?? 'Tanpa Judul' }}</div>
                        <div class="text-xs text-gray-500 truncate mt-0.5">
                            {{ optional($conv->lastMessage)->message ? Str::limit($conv->lastMessage->message, 80) : 'Belum ada pesan' }}
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                        <span class="{{ $statusConfig['color'] }} text-[11px] font-semibold px-2.5 py-1 rounded-full"><i class="fas {{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['label'] }}</span>
                        <div class="text-[11px] text-gray-400">{{ $conv->updated_at->diffForHumans() }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @if($conversations->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $conversations->links() }}</div>
            @endif
        </div>
    @endif
</div>
@endsection
