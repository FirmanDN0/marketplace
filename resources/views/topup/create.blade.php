@extends('layouts.app')
@section('title', 'Top Up Wallet')
@section('content')
<div class="max-w-xl mx-auto">


    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Top Up Balance</h2>
            <p class="text-gray-500 text-sm mt-1">Add funds to your wallet</p>
        </div>
            @endif

            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 mb-6">
                <div class="text-sm text-blue-600 font-medium">Current Balance</div>
                <div class="text-2xl font-bold text-blue-700">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
            </div>

            <form method="POST" action="{{ route('wallet.topup.store') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Amount</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                        @foreach([10000, 25000, 50000, 100000, 250000, 500000] as $preset)
                        <button type="button" onclick="setAmount({{ $preset }})" id="preset-{{ $preset }}"
                                class="border-2 border-gray-200 hover:border-blue-500 rounded-xl py-2.5 text-sm font-semibold text-gray-700 transition focus:outline-none">
                            Rp {{ number_format($preset, 0, ',', '.') }}
                        </button>
                        @endforeach
                    </div>
                    <input type="number" name="amount" id="amount"
                           placeholder="Or type custom amount (min Rp 10.000)"
                           value="{{ old('amount') }}" min="10000" max="100000000" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('amount')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-sm text-gray-600">
                    <i class="fas fa-shield-alt text-blue-600 mr-1"></i>
                    <strong>Payment via Midtrans</strong><br>
                    <span class="text-gray-400">Supports: Credit Card, Transfer, GoPay, QRIS, Alfamart, and more.</span>
                </div>

                <button type="submit" :disabled="submitting" :class="{'opacity-75 cursor-not-allowed': submitting}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition inline-flex items-center justify-center gap-2">
                    <span x-show="!submitting" class="inline-flex items-center gap-2">Continue to Payment <i class="fas fa-arrow-right"></i></span>
                    <span x-show="submitting" x-cloak class="inline-flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function setAmount(val) {
    document.getElementById('amount').value = val;
    document.querySelectorAll('[id^="preset-"]').forEach(b => {
        b.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
        b.classList.add('border-gray-200', 'text-gray-700');
    });
    const btn = document.getElementById('preset-' + val);
    if (btn) {
        btn.classList.remove('border-gray-200', 'text-gray-700');
        btn.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
    }
}
</script>
@endsection
