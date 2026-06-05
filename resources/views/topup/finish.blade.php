@extends('layouts.app')
@section('title', 'Hasil Top-Up')
@section('content')
<div class="max-w-xl mx-auto py-8 text-center">

    @if($topUp->isSuccess())
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 p-8">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-500 flex items-center justify-center mx-auto mb-5 shadow-sm">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">Top-Up Berhasil!</h2>
            <p class="text-gray-500 text-sm mb-4 font-medium">Dompet Anda telah diisi dengan</p>
            <div class="text-3xl font-extrabold text-emerald-600 mb-4">
                Rp {{ number_format($topUp->amount, 0, ',', '.') }}
            </div>
            <div class="bg-gray-50 rounded-2xl p-4 text-sm text-gray-600 text-left space-y-1 mb-6 border border-gray-100/80">
                <div><strong>Order ID:</strong> {{ $topUp->order_id }}</div>
                @if($topUp->payment_type)
                <div><strong>Metode:</strong> {{ str_replace('_', ' ', ucfirst($topUp->payment_type)) }}</div>
                @endif
                <div><strong>Waktu:</strong> {{ optional($topUp->paid_at)->format('d M Y, H:i') }}</div>
            </div>
            <a href="{{ route('wallet.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all inline-flex items-center gap-2 shadow-lg shadow-blue-500/15">
                <i class="fas fa-wallet"></i> Ke Dompet Saya
            </a>
        </div>

    @elseif($topUp->isPending())
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 p-8">
            <div class="w-16 h-16 rounded-2xl bg-yellow-100 text-yellow-500 flex items-center justify-center mx-auto mb-5 shadow-sm">
                <i class="fas fa-hourglass-half text-3xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">Menunggu Pembayaran</h2>
            <p class="text-gray-500 text-sm mb-2 font-medium">
                Pembayaran sebesar <strong>Rp {{ number_format($topUp->amount, 0, ',', '.') }}</strong>
                sedang diproses. Saldo akan diperbarui setelah dikonfirmasi.
            </p>
            <div class="text-xs text-gray-400 mb-6 font-medium">Order ID: {{ $topUp->order_id }}</div>

            @if($topUp->snap_token)
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 mb-6 text-sm text-yellow-700">
                <p class="mb-3 font-medium">Belum memilih metode pembayaran? Klik tombol di bawah untuk membuka kembali halaman pembayaran.</p>
                <button id="reopen-pay" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition">
                    Pilih Metode Pembayaran
                </button>
            </div>
            @endif

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('wallet.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15">Ke Dompet</a>
                <a href="{{ route('wallet.topup.create') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-2xl font-bold text-sm transition">Coba Lagi</a>
            </div>
        </div>

    @else
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 p-8">
            <div class="w-16 h-16 rounded-2xl bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-5 shadow-sm">
                <i class="fas fa-times-circle text-3xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">Top-Up Gagal</h2>
            <p class="text-gray-500 text-sm mb-6 font-medium">
                Top-up sebesar <strong>Rp {{ number_format($topUp->amount, 0, ',', '.') }}</strong>
                tidak berhasil. Tidak ada tagihan yang dibebankan.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('wallet.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15">Ke Dompet</a>
                <a href="{{ route('wallet.topup.create') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-2xl font-bold text-sm transition">Coba Lagi</a>
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
