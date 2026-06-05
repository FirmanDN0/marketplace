@extends('layouts.checkout')
@section('title', 'Pembayaran Berhasil')
@section('content')
@php $checkoutStep = 3; @endphp
<div class="max-w-md mx-auto py-8">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 p-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-500 flex items-center justify-center mx-auto mb-5 shadow-sm">
            <i class="fas fa-check-circle text-3xl"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">Pembayaran Berhasil!</h1>
        <p class="text-sm text-gray-500 mb-6 font-medium">Pesanan <strong class="text-gray-700">{{ $order->order_number }}</strong> telah dikonfirmasi. Penyedia jasa akan segera memulai pekerjaan.</p>

        <div class="bg-gray-50 rounded-2xl p-4 mb-6 border border-gray-100/80">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                <div><p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Layanan</p><p class="text-sm font-bold text-gray-900">{{ Str::limit(optional($order->service)->title, 35) }}</p></div>
                <div><p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Jumlah Dibayar</p><p class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">Lihat Pesanan</a>
            <a href="{{ route('customer.dashboard') }}" class="bg-white border border-gray-200/80 hover:border-blue-200 text-gray-700 px-5 py-2.5 rounded-2xl text-sm font-bold transition hover:-translate-y-0.5 active:translate-y-0 duration-300">Dasbor</a>
        </div>
    </div>
</div>
@endsection
