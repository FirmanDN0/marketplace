@extends('layouts.app')
@section('title', 'Tarik Dana')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100/80 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                <i class="fas fa-money-bill-wave text-sm"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Tarik Dana</h2>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Transfer saldo ke rekening bank atau e-wallet Anda</p>
            </div>
        </div>
        <div class="p-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl p-5 mb-6 text-white relative overflow-hidden">
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/[0.06] rounded-full"></div>
                <div class="relative">
                    <div class="text-blue-200/60 text-sm font-semibold mb-0.5">Saldo Tersedia</div>
                    <div class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
                </div>
            </div>

            @if($profile->balance < 50000)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-2xl text-sm font-medium">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Saldo tidak cukup. Minimum penarikan adalah Rp 50.000.
                </div>
            @else
            <form method="POST" action="{{ route('wallet.withdraw.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Jumlah Penarikan <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" placeholder="Min Rp 50.000" value="{{ old('amount') }}"
                           min="50000" max="{{ $profile->balance }}" required
                           class="w-full rounded-2xl border border-gray-200/80 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('amount')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="method" required class="w-full rounded-2xl border border-gray-200/80 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Pilih Metode --</option>
                        <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="gopay" {{ old('method') === 'gopay' ? 'selected' : '' }}>GoPay</option>
                        <option value="dana" {{ old('method') === 'dana' ? 'selected' : '' }}>DANA</option>
                        <option value="paypal" {{ old('method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                    @error('method')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Pemilik Akun <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" placeholder="Nama lengkap sesuai rekening" value="{{ old('account_name') }}" required maxlength="100"
                           class="w-full rounded-2xl border border-gray-200/80 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('account_name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nomor Rekening / Akun <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" placeholder="Nomor rekening bank atau nomor telepon" value="{{ old('account_number') }}" required maxlength="100"
                           class="w-full rounded-2xl border border-gray-200/80 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('account_number')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3 rounded-2xl text-sm font-medium">
                    <i class="fas fa-info-circle mr-1"></i> Penarikan dana diproses oleh admin dalam 1–3 hari kerja.
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
                    Ajukan Penarikan Dana
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
