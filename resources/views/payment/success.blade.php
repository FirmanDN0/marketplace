@extends('layouts.app')
@section('title', 'Payment Successful')
@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-green-100 text-green-500 flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-check-circle text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
        <p class="text-sm text-gray-500 mb-6">Order <strong class="text-gray-700">{{ $order->order_number }}</strong> is confirmed. The provider will start working soon.</p>

        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                <div><p class="text-xs text-gray-400">SERVICE</p><p class="text-sm font-medium text-gray-900">{{ Str::limit(optional($order->service)->title, 35) }}</p></div>
                <div><p class="text-xs text-gray-400">AMOUNT PAID</p><p class="text-sm font-bold text-green-600">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">View Order</a>
            <a href="{{ route('customer.dashboard') }}" class="bg-white border border-gray-200 hover:border-blue-300 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition">Dashboard</a>
        </div>
    </div>
</div>
@endsection
