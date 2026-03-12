@extends('layouts.app')
@section('title', 'Payment Failed')
@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-times-circle text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
        <p class="text-sm text-gray-500 mb-6">Order <strong class="text-gray-700">{{ $order->order_number }}</strong> payment was not completed. Please try again or contact support.</p>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('payment.show', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">Try Again</a>
            <a href="{{ route('customer.orders.index') }}" class="bg-white border border-gray-200 hover:border-blue-300 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition">My Orders</a>
        </div>
    </div>
</div>
@endsection
