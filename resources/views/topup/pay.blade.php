@extends('layouts.app')
@section('title', 'Complete Payment')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Complete Top-Up Payment</h2>
        </div>
        <div class="p-6 text-center">
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <div class="text-sm text-blue-600 font-medium mb-1">Top-Up Amount</div>
                <div class="text-3xl font-bold text-blue-700">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</div>
                <div class="text-xs text-blue-400 mt-1">Order ID: {{ $topUp->order_id }}</div>
            </div>

            <p class="text-sm text-gray-500 mb-6">Click the button below to open the Midtrans payment page. Complete the payment in the popup window.</p>

            <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                <i class="fas fa-credit-card"></i> Pay Now via Midtrans
            </button>

            <div class="mt-4">
                <a href="{{ route('wallet.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition">Cancel and go back</a>
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
            window.location.href = '{{ route('wallet.topup.finish', $topUp->id) }}?status=success';
        },
        onPending: function(result) {
            window.location.href = '{{ route('wallet.topup.finish', $topUp->id) }}?status=pending';
        },
        onError: function(result) {
            window.location.href = '{{ route('wallet.topup.finish', $topUp->id) }}?status=failed';
        },
        onClose: function() {}
    });
};
</script>
@endsection
