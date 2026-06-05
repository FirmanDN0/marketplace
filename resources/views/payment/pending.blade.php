@extends('layouts.checkout')
@section('title', 'Menunggu Pembayaran')
@section('content')
@php $checkoutStep = 2; @endphp
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden text-center">
        <div class="p-8">
            <div class="w-16 h-16 rounded-2xl bg-yellow-100 flex items-center justify-center mx-auto mb-5 shadow-sm">
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">Menunggu Pembayaran</h2>
            <p class="text-gray-500 text-sm mb-6 font-medium">Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran jika belum.</p>

            <div class="bg-gray-50 rounded-2xl p-4 mb-6 border border-gray-100/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                    <div><p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pesanan</p><p class="text-sm font-bold text-gray-900">{{ $order->order_number }}</p></div>
                    <div><p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Jumlah</p><p class="text-sm font-extrabold text-yellow-600">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('payment.show', $order->id) }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all text-center shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
                    <i class="fas fa-redo mr-2"></i>Coba Lagi
                </a>
                <a href="{{ route('customer.orders.show', $order->id) }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-2xl font-bold text-sm transition text-center">
                    Lihat Detail Pesanan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
