@extends('layouts.app')
@section('title', 'Top-Up Result')
@section('content')
<div class="max-w-xl mx-auto py-8 text-center">

    @if($topUp->isSuccess())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="w-20 h-20 rounded-full bg-green-100 text-green-500 flex items-center justify-center mx-auto mb-4 text-4xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Top-Up Successful!</h2>
            <p class="text-gray-500 text-sm mb-4">Your wallet has been topped up with</p>
            <div class="text-3xl font-bold text-green-600 mb-4">
                Rp {{ number_format($topUp->amount, 0, ',', '.') }}
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 text-left space-y-1 mb-6">
                <div><strong>Order ID:</strong> {{ $topUp->order_id }}</div>
                @if($topUp->payment_type)
                <div><strong>Method:</strong> {{ str_replace('_', ' ', ucfirst($topUp->payment_type)) }}</div>
                @endif
                <div><strong>Time:</strong> {{ optional($topUp->paid_at)->format('d M Y, H:i') }}</div>
            </div>
            <a href="{{ route('wallet.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fas fa-wallet"></i> Go to Wallet
            </a>
        </div>

    @elseif($topUp->isPending())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="w-20 h-20 rounded-full bg-yellow-100 text-yellow-500 flex items-center justify-center mx-auto mb-4 text-4xl">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Pending</h2>
            <p class="text-gray-500 text-sm mb-2">
                Your payment of <strong>Rp {{ number_format($topUp->amount, 0, ',', '.') }}</strong>
                is being processed. Your balance will be updated once confirmed.
            </p>
            <div class="text-xs text-gray-400 mb-6">Order ID: {{ $topUp->order_id }}</div>

            @if($topUp->snap_token)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 text-sm text-yellow-700">
                <p class="mb-3">Belum memilih metode pembayaran? Klik tombol di bawah untuk membuka kembali halaman pembayaran.</p>
                <button id="reopen-pay" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                    Pilih Metode Pembayaran
                </button>
            </div>
            @endif

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('wallet.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition">Go to Wallet</a>
                <a href="{{ route('wallet.topup.create') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-medium text-sm transition">Try Again</a>
            </div>
        </div>

    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="w-20 h-20 rounded-full bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-4 text-4xl">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Top-Up Failed</h2>
            <p class="text-gray-500 text-sm mb-6">
                Your top-up of <strong>Rp {{ number_format($topUp->amount, 0, ',', '.') }}</strong>
                was not completed. No charges were made.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('wallet.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition">Go to Wallet</a>
                <a href="{{ route('wallet.topup.create') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-medium text-sm transition">Try Again</a>
            </div>
        </div>
    @endif

</div>

@if($topUp->isPending() && $topUp->snap_token)
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
document.getElementById('reopen-pay').onclick = function () {
    snap.pay('{{ $topUp->snap_token }}', {
        onSuccess: function(result) { window.location.reload(); },
        onPending: function(result) { window.location.reload(); },
        onError: function(result) { window.location.reload(); },
        onClose: function() {}
    });
};
</script>
@endif
@endsection
