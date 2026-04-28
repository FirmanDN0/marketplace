@extends('layouts.app')
@section('title', 'Request Withdrawal')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Request Withdrawal</h2>
            <p class="text-gray-500 text-sm mt-1">Transfer your balance to your bank or e-wallet</p>
        </div>
        <div class="p-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 mb-6">
                <div class="text-sm text-blue-600 font-medium">Available Balance</div>
                <div class="text-2xl font-bold text-blue-700">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
            </div>

            @if($profile->balance < 50000)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Insufficient balance. Minimum withdrawal is Rp 50.000.
                </div>
            @else
            <form method="POST" action="{{ route('wallet.withdraw.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Withdrawal Amount <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" placeholder="Min Rp 50.000" value="{{ old('amount') }}"
                           min="50000" max="{{ $profile->balance }}" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('amount')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                    <select name="method" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select Method --</option>
                        <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="gopay" {{ old('method') === 'gopay' ? 'selected' : '' }}>GoPay</option>
                        <option value="dana" {{ old('method') === 'dana' ? 'selected' : '' }}>DANA</option>
                        <option value="paypal" {{ old('method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                    @error('method')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Account / Wallet Name <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" placeholder="Full name on account" value="{{ old('account_name') }}" required maxlength="100"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('account_name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Account / Wallet Number <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" placeholder="Bank account or phone number" value="{{ old('account_number') }}" required maxlength="100"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('account_number')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3 rounded-xl text-sm">
                    <i class="fas fa-info-circle mr-1"></i> Withdrawal requests are processed by admin within 1–3 business days.
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
                    Submit Withdrawal Request
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
