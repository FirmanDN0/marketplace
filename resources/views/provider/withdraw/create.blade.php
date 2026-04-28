@extends('layouts.app')
@section('title', 'Request Withdrawal')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Request Withdrawal</h1>
    </div>

    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-6">
        <p class="text-sm text-green-700">Available Balance: <strong class="text-green-800">Rp {{ number_format($profile->balance, 0, ',', '.') }}</strong></p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('provider.withdraw.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Withdrawal Amount (Rp)</label>
                    <input id="amount" type="number" name="amount"
                           min="10000" max="{{ $profile->balance }}" step="1000" required
                           placeholder="Min Rp 10.000"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Minimum Rp 10.000 &bull; Maximum Rp {{ number_format($profile->balance, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 mb-1.5">Payment Method</label>
                    <select id="method" name="method" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="paypal">PayPal</option>
                        <option value="gopay">GoPay</option>
                        <option value="dana">Dana</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1.5">Account Name</label>
                        <input id="account_name" type="text" name="account_name" required placeholder="Full name on account"
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1.5">Account Number / Email</label>
                        <input id="account_number" type="text" name="account_number" required placeholder="Account no. or email"
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">Submit Request</button>
                    <a href="{{ route('provider.withdraw.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
