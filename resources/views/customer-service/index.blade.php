@extends('layouts.app')
@section('title', 'Customer Service')
@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-comments text-blue-600 mr-2"></i>Customer Service</h1>
            <p class="text-gray-500 text-sm mt-1">Tanya apa saja — AI kami siap membantu 24/7</p>
        </div>
        <a href="{{ route('customer-service.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2 self-start sm:self-auto">
            <i class="fas fa-plus"></i> Percakapan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('success') }}</div>
    @endif

    @if($conversations->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-blue-50 text-blue-400 flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="fas fa-robot"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada percakapan</h3>
            <p class="text-gray-500 text-sm mb-5">Mulai percakapan baru untuk mendapatkan bantuan dari AI Customer Service kami.</p>
            <a href="{{ route('customer-service.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition">Mulai Sekarang</a>
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
                    $statusColor = match($conv->status) {
                        'ai'     => 'bg-indigo-100 text-indigo-700',
                        'human'  => 'bg-yellow-100 text-yellow-700',
                        'closed' => 'bg-gray-100 text-gray-500',
                        default  => 'bg-gray-100 text-gray-500',
                    };
                    $statusLabel = match($conv->status) {
                        'ai'     => '<i class="fas fa-robot"></i> AI',
                        'human'  => '<i class="fas fa-user"></i> Menunggu Agen',
                        'closed' => '<i class="fas fa-check-circle"></i> Selesai',
                        default  => $conv->status,
                    };
                @endphp
                <a href="{{ route('customer-service.show', $conv->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-blue-50/50 transition group">
                    <div class="w-10 h-10 rounded-full {{ $conv->status === 'ai' ? 'bg-indigo-100 text-indigo-600' : ($conv->status === 'human' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-400') }} flex items-center justify-center flex-shrink-0">
                        {!! $conv->status === 'ai' ? '<i class="fas fa-robot"></i>' : ($conv->status === 'human' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-check-circle"></i>') !!}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 group-hover:text-blue-600 transition text-sm">{{ $conv->subject ?? 'Tanpa Judul' }}</div>
                        <div class="text-xs text-gray-500 truncate mt-0.5">
                            {{ optional($conv->lastMessage)->message ? Str::limit($conv->lastMessage->message, 80) : 'Belum ada pesan' }}
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span class="{{ $statusColor }} text-xs font-semibold px-2.5 py-1 rounded-full">{!! $statusLabel !!}</span>
                        <div class="text-xs text-gray-400">{{ $conv->updated_at->diffForHumans() }}</div>
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
