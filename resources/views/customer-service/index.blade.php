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

    {{-- Tab Navigation --}}
    <div x-data="{ activeTab: 'active' }">
        <div class="flex items-center gap-1 mb-6 p-1 bg-gray-100 rounded-2xl w-fit">
            <button @click="activeTab = 'active'" 
                    :class="activeTab === 'active' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-comment-dots"></i>
                Aktif
                <span class="ml-1 px-2 py-0.5 rounded-lg text-[10px] bg-blue-50 text-blue-600">{{ $activeConversations->count() }}</span>
            </button>
            <button @click="activeTab = 'closed'" 
                    :class="activeTab === 'closed' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                Selesai
                <span class="ml-1 px-2 py-0.5 rounded-lg text-[10px] bg-gray-200 text-gray-500">{{ $closedConversations->total() }}</span>
            </button>
        </div>

        {{-- Active Tab Content --}}
        <div x-show="activeTab === 'active'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($activeConversations->isEmpty())
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 py-20 text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-300 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-comment-slash"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Tidak ada chat aktif</h3>
                    <p class="text-gray-400 text-sm mb-6 max-w-xs mx-auto">Butuh bantuan? Mulai percakapan baru dengan AI Assistant kami.</p>
                    <a href="{{ route('customer-service.start') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition inline-flex items-center gap-2 shadow-lg shadow-blue-200">
                        Mulai Chat Baru
                    </a>
                </div>
            @else
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
                    @foreach($activeConversations as $conv)
                        @include('customer-service._conversation_item', ['conv' => $conv])
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Closed Tab Content --}}
        <div x-show="activeTab === 'closed'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($closedConversations->isEmpty())
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 py-20 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Riwayat masih kosong</h3>
                    <p class="text-gray-400 text-sm max-w-xs mx-auto">Percakapan yang sudah selesai akan muncul di sini.</p>
                </div>
            @else
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
                    @foreach($closedConversations as $conv)
                        @include('customer-service._conversation_item', ['conv' => $conv])
                    @endforeach
                </div>
                @if($closedConversations->hasPages())
                    <div class="mt-6">
                        {{ $closedConversations->appends(['closed_page' => $closedConversations->currentPage()])->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
