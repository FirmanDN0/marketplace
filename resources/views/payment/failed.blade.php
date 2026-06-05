@extends('layouts.checkout')
@section('title', 'Pembayaran Gagal')
@section('content')
@php $checkoutStep = 2; @endphp
<div class="max-w-md mx-auto py-12">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 p-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-5 shadow-sm">
            <i class="fas fa-times-circle text-3xl"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">Pembayaran Gagal</h1>
        <p class="text-sm text-gray-500 mb-6 font-medium">Pembayaran untuk pesanan <strong class="text-gray-700">{{ $order->order_number }}</strong> tidak berhasil. Silakan coba lagi atau hubungi dukungan.</p>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('payment.show', $order->id) }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">Coba Lagi</a>
            <a href="{{ route('customer.orders.index') }}" class="bg-white border border-gray-200/80 hover:border-blue-200 text-gray-700 px-5 py-2.5 rounded-2xl text-sm font-bold transition hover:-translate-y-0.5 active:translate-y-0 duration-300">Pesanan Saya</a>
        </div>
    </div>
</div>
@endsection
