@extends('layouts.app')
@section('title', 'Payment Pending')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-center">
        <div class="p-8">
            <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Pending</h2>
            <p class="text-gray-500 text-sm mb-6">Your payment is being processed. Please complete the payment if you haven't already.</p>

            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                    <div><p class="text-xs text-gray-400">ORDER</p><p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p></div>
                    <div><p class="text-xs text-gray-400">AMOUNT</p><p class="text-sm font-bold text-yellow-600">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('payment.show', $order->id) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition text-center">
                    <i class="fas fa-redo mr-2"></i>Try Again
                </a>
                <a href="{{ route('customer.orders.show', $order->id) }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold text-sm transition text-center">
                    View Order Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
