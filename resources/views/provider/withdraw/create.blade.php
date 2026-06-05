@extends('layouts.app')
@section('title', 'Ajukan Penarikan')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
            <i class="fas fa-money-bill-wave text-sm"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Ajukan Penarikan Dana</h1>
    </div>

    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl px-5 py-4 mb-6 text-white relative overflow-hidden">
        <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/[0.06] rounded-full"></div>
        <div class="relative">
            <p class="text-sm text-blue-200/60 font-semibold">Saldo Tersedia</p>
            <p class="text-xl font-extrabold">Rp {{ number_format($profile->balance, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('provider.withdraw.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="amount" class="block text-sm font-bold text-gray-700 mb-1.5">Jumlah Penarikan (Rp)</label>
                    <input id="amount" type="number" name="amount"
                           min="10000" max="{{ $profile->balance }}" step="1000" required
                           placeholder="Min Rp 10.000"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Minimum Rp 10.000 &bull; Maximum Rp {{ number_format($profile->balance, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label for="method" class="block text-sm font-bold text-gray-700 mb-1.5">Metode Pembayaran</label>
                    <select id="method" name="method" required class="w-full rounded-2xl border border-gray-200/80 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="paypal">PayPal</option>
                        <option value="gopay">GoPay</option>
                        <option value="dana">Dana</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="account_name" class="block text-sm font-bold text-gray-700 mb-1.5">Nama Pemilik Akun</label>
                        <input id="account_name" type="text" name="account_name" required placeholder="Nama lengkap sesuai akun"
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-bold text-gray-700 mb-1.5">Nomor Akun / Email</label>
                        <input id="account_number" type="text" name="account_number" required placeholder="No. rekening atau email"
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15">Ajukan Penarikan</button>
                    <a href="{{ route('provider.withdraw.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-bold transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
