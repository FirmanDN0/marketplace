@extends('layouts.app')
@section('title', 'Payment')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-center text-white">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold">Payment Gateway</h2>
            <p class="text-blue-200 text-sm mt-1">Simulated Payment &mdash; Production would use Midtrans/Stripe</p>
        </div>
        <div class="p-6">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Order Number</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Amount Due</span>
                    <span class="font-bold text-blue-600 text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Service</span>
                    <span class="text-gray-700 text-sm">{{ Str::limit(optional($order->service)->title,40) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Package</span>
                    <span class="text-gray-700 text-sm">{{ optional($order->package)->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Method</span>
                    <span class="text-gray-700 text-sm">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-xs uppercase text-gray-400 font-medium">Token</span>
                    <span class="text-gray-500 text-xs font-mono">{{ Str::limit($payment->payment_token,20) }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <form method="POST" action="{{ route('payment.process', $order->id) }}">
                    @csrf
                    <input type="hidden" name="action" value="pay">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Simulate Successful Payment
                    </button>
                </form>
                <form method="POST" action="{{ route('payment.process', $order->id) }}">
                    @csrf
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-6 py-3 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2 border border-red-200">
                        <i class="fas fa-times"></i> Simulate Failed Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
