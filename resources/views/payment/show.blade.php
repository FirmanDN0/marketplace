@extends('layouts.checkout')
@section('title', 'Pembayaran')
@section('content')
@php $checkoutStep = 2; @endphp
<div class="max-w-xl mx-auto py-4">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-6 py-8 text-center text-white relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/[0.06] rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-white/[0.04] rounded-full"></div>
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-900/20">
                    <i class="fas fa-credit-card text-2xl"></i>
                </div>
                <h2 class="text-xl font-extrabold tracking-tight">Pembayaran</h2>
                <p class="text-blue-200/70 text-sm mt-1 font-medium">Pilih metode pembayaran</p>
            </div>
        </div>
        <div class="p-6">
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-400"></i> {{ session('error') }}
                </div>
            @endif

            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Order Number</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-xs uppercase text-gray-400 font-medium">Total Bayar</span>
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
            </div>

            {{-- Payment Methods --}}
            <div x-data="{ method: 'midtrans' }" class="space-y-3">
                <p class="text-sm font-bold text-gray-700 mb-2">Pilih Metode Pembayaran</p>

                {{-- Wallet Option --}}
                <label class="flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition"
                       :class="method === 'wallet' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="method" value="wallet" x-model="method" class="text-green-600 focus:ring-green-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-wallet text-green-600"></i>
                            <span class="font-semibold text-gray-900 text-sm">Bayar dengan Saldo</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-gray-500">Saldo kamu saat ini</span>
                            <span class="font-bold text-sm {{ $walletBalance >= $payment->amount ? 'text-green-600' : 'text-red-500' }}">
                                Rp {{ number_format($walletBalance, 0, ',', '.') }}
                            </span>
                        </div>
                        @if ($walletBalance < $payment->amount)
                            <p class="text-xs text-red-500 mt-1">
                                <i class="fas fa-info-circle"></i> Saldo kurang Rp {{ number_format($payment->amount - $walletBalance, 0, ',', '.') }}.
                                <a href="{{ route('wallet.topup.create') }}" class="underline font-semibold">Top up sekarang</a>
                            </p>
                        @endif
                    </div>
                </label>

                {{-- Midtrans Option --}}
                <label class="flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition"
                       :class="method === 'midtrans' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="method" value="midtrans" x-model="method" class="text-blue-600 focus:ring-blue-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-university text-blue-600"></i>
                            <span class="font-semibold text-gray-900 text-sm">Payment Gateway (Midtrans)</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Transfer Bank, E-Wallet, Kartu Kredit, QRIS, dll.</p>
                    </div>
                </label>

                {{-- Wallet Pay Button --}}
                <div x-show="method === 'wallet'" x-cloak>
                    <form method="POST" action="{{ route('payment.wallet', $order->id) }}">
                        @csrf
                        <button type="submit" {{ $walletBalance < $payment->amount ? 'disabled' : '' }}
                                class="w-full px-6 py-3.5 rounded-2xl font-bold text-sm transition-all inline-flex items-center justify-center gap-2
                                {{ $walletBalance >= $payment->amount ? 'bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white shadow-lg shadow-green-500/15' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                            <i class="fas fa-wallet"></i> Bayar dengan Saldo
                        </button>
                    </form>
                </div>

                {{-- Midtrans Pay Button --}}
                <div x-show="method === 'midtrans'" x-cloak>
                    <button id="pay-button" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-3.5 rounded-2xl font-bold text-sm transition-all inline-flex items-center justify-center gap-2 shadow-lg shadow-blue-500/15">
                        <i class="fas fa-lock"></i> Bayar via Midtrans
                    </button>
                </div>

                <a href="{{ route('customer.orders.index') }}" class="block text-center text-sm text-gray-500 hover:text-blue-600 transition mt-2 font-medium">
                    Batal dan kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
document.getElementById('pay-button').onclick = function () {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route('payment.finish', $order->id) }}?status=success';
        },
        onPending: function(result) {
            window.location.href = '{{ route('payment.finish', $order->id) }}?status=pending';
        },
        onError: function(result) {
            window.location.href = '{{ route('payment.finish', $order->id) }}?status=failed';
        },
        onClose: function() {}
    });
};
</script>
@endsection
