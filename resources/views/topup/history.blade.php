@extends('layouts.app')
@section('title', 'Top-Up History')
@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('wallet.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition inline-flex items-center gap-1.5 mb-2">
                <i class="fas fa-arrow-left"></i> Back to Wallet
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Top-Up History</h1>
        </div>
        <a href="{{ route('wallet.topup.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2">
            <i class="fas fa-plus"></i> New Top Up
        </a>
    </div>

    {{-- Balance Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Available Balance</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Total Topped Up</div>
            <div class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($topUps->getCollection()->where('status','success')->sum('amount'), 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-sm text-gray-500">All Transactions</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $topUps->total() }}</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <span class="text-sm text-gray-500 py-1.5">Filter:</span>
        @foreach([''=>'All', 'success'=>'Success', 'pending'=>'Pending', 'failed'=>'Failed', 'expired'=>'Expired'] as $val => $label)
        <a href="{{ route('wallet.topup.history', $val ? ['status' => $val] : []) }}"
           class="px-3.5 py-1.5 rounded-full text-sm font-medium transition {{ request('status') === $val || (!request('status') && $val === '') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- History Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Order ID</th>
                        <th class="px-5 py-3 text-left font-medium">Amount</th>
                        <th class="px-5 py-3 text-left font-medium">Method</th>
                        <th class="px-5 py-3 text-left font-medium">Status</th>
                        <th class="px-5 py-3 text-left font-medium">Date</th>
                        <th class="px-5 py-3 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($topUps as $topUp)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $topUp->order_id }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-900">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $topUp->payment_type ? str_replace('_', ' ', ucfirst($topUp->payment_type)) : '-' }}</td>
                    <td class="px-5 py-3">
                        @php
                            $badge = match($topUp->status) {
                                'success' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-red-100 text-red-700',
                            };
                        @endphp
                        <span class="{{ $badge }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ strtoupper($topUp->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">
                        {{ $topUp->created_at->format('d M Y, H:i') }}
                        @if($topUp->paid_at)
                            <div class="text-xs text-gray-400">Paid: {{ $topUp->paid_at->format('d M Y, H:i') }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($topUp->isPending() && $topUp->snap_token)
                            <a href="{{ route('wallet.topup.finish', $topUp->id) }}" class="text-blue-600 hover:text-blue-700 font-medium text-xs">Check Status</a>
                        @elseif($topUp->isSuccess())
                            <span class="text-green-500 text-xs"><i class="fas fa-check"></i> Done</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="text-4xl text-gray-300 mb-3"><i class="fas fa-clipboard-list"></i></div>
                        <div class="font-semibold text-gray-600 mb-1">No top-up records yet</div>
                        <div class="text-sm text-gray-400 mb-4">Make your first top-up to see history here.</div>
                        <a href="{{ route('wallet.topup.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition">Top Up Now</a>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($topUps->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $topUps->links() }}</div>
        @endif
    </div>

</div>
@endsection
