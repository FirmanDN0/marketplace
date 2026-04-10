@extends('layouts.app')
@section('title', 'Payment')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-center text-white">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold">Pembayaran</h2>
            <p class="text-blue-200 text-sm mt-1">Pilih metode pembayaran</p>
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
                <p class="text-sm font-semibold text-gray-700 mb-2">Pilih Metode Pembayaran</p>

                {{-- Wallet Option --}}
                <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition"
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
                <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition"
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
                                class="w-full px-6 py-3.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2
                                {{ $walletBalance >= $payment->amount ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                            <i class="fas fa-wallet"></i> Bayar dengan Saldo
                        </button>
                    </form>
                </div>

                {{-- Midtrans Pay Button --}}
                <div x-show="method === 'midtrans'" x-cloak>
                    <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i> Bayar via Midtrans
                    </button>
                </div>

                <a href="{{ route('customer.orders.index') }}" class="block text-center text-sm text-gray-500 hover:text-blue-600 transition mt-2">
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
