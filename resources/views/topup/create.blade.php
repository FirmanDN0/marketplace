@extends('layouts.app')
@section('title', 'Isi Saldo')
@section('content')
<div class="max-w-xl mx-auto">

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100/80 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                <i class="fas fa-plus text-sm"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Isi Saldo</h2>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Tambahkan dana ke dompet Anda</p>
            </div>
        </div>

        <div class="px-6 py-5">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl p-5 mb-6 text-white relative overflow-hidden">
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/[0.06] rounded-full"></div>
                <div class="relative">
                    <div class="text-blue-200/60 text-sm font-semibold mb-0.5">Saldo Saat Ini</div>
                    <div class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('wallet.topup.store') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Nominal</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                        @foreach([1, 100, 500, 1000, 5000, 10000] as $preset)
                        <button type="button" onclick="setAmount({{ $preset }})" id="preset-{{ $preset }}"
                                class="border-2 border-gray-200/80 hover:border-blue-500 rounded-2xl py-2.5 text-sm font-bold text-gray-700 transition focus:outline-none">
                            Rp {{ number_format($preset, 0, ',', '.') }}
                        </button>
                        @endforeach
                    </div>
                    <input type="number" name="amount" id="amount"
                           placeholder="Atau ketik nominal sendiri (min Rp 1)"
                           value="{{ old('amount') }}" min="1" max="100000000" required
                           class="w-full rounded-2xl border border-gray-200/80 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('amount')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="bg-gray-50 border border-gray-100/80 rounded-2xl p-4 text-sm text-gray-600">
                    <i class="fas fa-qrcode text-blue-600 mr-1"></i>
                    <strong>Pembayaran via E-Wallet / QRIS</strong><br>
                    <span class="text-gray-400">Mendukung: GoPay, OVO, Dana, LinkAja, ShopeePay, dan lainnya.</span>
                </div>

                <button type="submit" :disabled="submitting" :class="{'opacity-75 cursor-not-allowed': submitting}" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all inline-flex items-center justify-center gap-2 shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
                    <span x-show="!submitting" class="inline-flex items-center gap-2">Lanjutkan ke Pembayaran <i class="fas fa-arrow-right"></i></span>
                    <span x-show="submitting" x-cloak class="inline-flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
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
