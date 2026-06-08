@extends('layouts.app')
@section('title', 'Selesaikan Pembayaran')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100/80 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                <i class="fas fa-credit-card text-sm"></i>
            </div>
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Selesaikan Pembayaran Top-Up</h2>
        </div>
        <div class="p-6 text-center">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/[0.06] rounded-full"></div>
                <div class="relative">
                    <div class="text-blue-200/60 text-sm font-semibold mb-1">Jumlah Top-Up</div>
                    <div class="text-3xl font-extrabold tracking-tight">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</div>
                    <div class="text-xs text-blue-200/40 mt-1 font-medium">Order ID: {{ $topUp->order_id }}</div>
                </div>
            </div>

            @if($qrisString)
            <div class="mb-6 flex flex-col items-center">
                <p class="text-sm text-gray-500 mb-4 font-medium">Scan QRIS di bawah ini dengan aplikasi E-Wallet kesayangan Anda.</p>
                <div class="p-4 bg-white border-2 border-dashed border-gray-300 rounded-2xl shadow-sm inline-block">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrisString) }}" alt="QRIS" class="w-48 h-48 rounded-xl object-contain">
                </div>
            </div>
            
            <button onclick="window.location.reload()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-3.5 rounded-2xl font-bold text-sm transition-all inline-flex items-center justify-center gap-2 shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
                <i class="fas fa-sync-alt"></i> Cek Status Pembayaran
            </button>
            @else
            <p class="text-sm text-red-500 mb-6 font-medium">Maaf, gagal menampilkan QRIS. Silakan coba kembali.</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('wallet.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition font-medium">Batalkan dan kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
