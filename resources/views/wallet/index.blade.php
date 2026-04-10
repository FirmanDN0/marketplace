@extends('layouts.app')
@section('title', 'My Wallet')
@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Wallet</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your balance and transactions</p>
        </div>
        <a href="{{ route('wallet.topup.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition inline-flex items-center gap-2 self-start sm:self-auto">
            <i class="fas fa-plus"></i> Top Up
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Balance Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg">
            <div class="text-blue-200 text-sm font-medium mb-1">Available Balance</div>
            <div class="text-2xl sm:text-3xl font-bold mb-4">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('wallet.topup.create') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-plus mr-1"></i> Top Up
                </a>
                <a href="{{ route('wallet.withdraw.create') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-university mr-1"></i> Withdraw
                </a>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="text-gray-500 text-sm font-medium mb-1">Total Top-Up (all time)</div>
            <div class="text-2xl font-bold text-gray-900 mb-1">
                Rp {{ number_format($topUps->where('status','success')->sum('amount'), 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-400">{{ $topUps->where('status','success')->count() }} successful top-up(s)</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Top-Up History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Top-Up History</h3>
                <div class="flex items-center gap-3">
                    <a href="{{ route('wallet.topup.history') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all</a>
                    <a href="{{ route('wallet.topup.create') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ New</a>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topUps as $topUp)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400">{{ $topUp->created_at->format('d M Y, H:i') }}</div>
                        @if($topUp->payment_type)
                            <div class="text-xs text-gray-400">{{ str_replace('_',' ',$topUp->payment_type) }}</div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @php $tc = match($topUp->status) { 'success' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                        <span class="{{ $tc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ strtoupper($topUp->status) }}</span>
                        @if($topUp->isPending())
                            <a href="{{ route('wallet.topup.finish', $topUp->id) }}" class="text-xs text-blue-600 hover:underline font-medium">Cek Status</a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-sm">No top-ups yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Transaction History</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($transactions as $tx)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <div class="text-sm text-gray-700">{{ $tx->description }}</div>
                        <div class="text-xs text-gray-400">{{ $tx->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="font-semibold text-sm {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $tx->amount > 0 ? '+' : '' }}Rp {{ number_format(abs($tx->amount), 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-sm">No transactions yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Withdrawal History --}}
    @if($withdrawals->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Withdrawal Requests</h3>
            <a href="{{ route('wallet.withdraw.create') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ New</a>
        </div>
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @foreach($withdrawals as $wd)
            <div class="px-4 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-900">Rp {{ number_format($wd->amount, 0, ',', '.') }}</span>
                    @php $wc = match($wd->status) { 'processed' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-blue-100 text-blue-700', default => 'bg-red-100 text-red-700' }; @endphp
                    <span class="{{ $wc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst($wd->status) }}</span>
                </div>
                <div class="text-sm text-gray-600">{{ str_replace('_',' ', ucfirst($wd->method)) }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $wd->created_at->format('d M Y') }}</div>
            </div>
            @endforeach
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Amount</th>
                        <th class="px-5 py-3 text-left font-medium">Method</th>
                        <th class="px-5 py-3 text-left font-medium hidden md:table-cell">Account</th>
                        <th class="px-5 py-3 text-left font-medium">Status</th>
                        <th class="px-5 py-3 text-left font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @foreach($withdrawals as $wd)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-semibold text-gray-900">Rp {{ number_format($wd->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ str_replace('_',' ', ucfirst($wd->method)) }}</td>
                    <td class="px-5 py-3 text-gray-600 hidden md:table-cell">{{ optional($wd->account_details)['name'] ?? '-' }} / {{ optional($wd->account_details)['number'] ?? '-' }}</td>
                    <td class="px-5 py-3">
                        @php $wc = match($wd->status) { 'processed' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-blue-100 text-blue-700', default => 'bg-red-100 text-red-700' }; @endphp
                        <span class="{{ $wc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst($wd->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $wd->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
