@extends('layouts.app')
@section('title', 'Dompet Saya')
@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Dompet Saya</h1>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Kelola saldo dan riwayat transaksi</p>
            </div>
        </div>
        <a href="{{ route('wallet.topup.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all inline-flex items-center gap-2 self-start sm:self-auto shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">
            <i class="fas fa-plus"></i> Isi Saldo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Balance Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-3xl p-6 text-white shadow-xl shadow-blue-600/15 relative overflow-hidden">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/[0.06] rounded-full"></div>
                <div class="absolute -bottom-8 -left-8 w-28 h-28 bg-white/[0.04] rounded-full"></div>
            </div>
            <div class="relative">
                <div class="text-blue-200/60 text-sm font-semibold mb-1">Saldo Tersedia</div>
                <div class="text-2xl sm:text-3xl font-extrabold mb-4 tracking-tight">Rp {{ number_format($profile->balance, 0, ',', '.') }}</div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('wallet.topup.create') }}" class="bg-white/15 hover:bg-white/25 backdrop-blur-sm border border-white/20 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 active:translate-y-0 duration-300">
                        <i class="fas fa-plus mr-1"></i> Isi Saldo
                    </a>
                    <a href="{{ route('wallet.withdraw.create') }}" class="bg-white/15 hover:bg-white/25 backdrop-blur-sm border border-white/20 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 active:translate-y-0 duration-300">
                        <i class="fas fa-university mr-1"></i> Tarik Dana
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm">
            <div class="text-gray-500 text-sm font-semibold mb-1">Total Top-Up (Sepanjang Waktu)</div>
            <div class="text-2xl font-extrabold text-gray-900 mb-1">
                Rp {{ number_format($topUps->where('status','success')->sum('amount'), 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-400 font-medium">{{ $topUps->where('status','success')->count() }} top-up berhasil</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Top-Up History --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100/80">
                <h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-arrow-circle-up text-emerald-500 text-sm"></i>Riwayat Top-Up</h3>
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
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100/80">
                <h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-exchange-alt text-blue-500 text-sm"></i>Riwayat Transaksi</h3>
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
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100/80">
            <h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-money-bill-wave text-amber-500 text-sm"></i>Riwayat Penarikan</h3>
            <a href="{{ route('wallet.withdraw.create') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ New</a>
        </div>
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @foreach($withdrawals as $wd)
            <div class="px-4 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-900">Rp {{ number_format($wd->amount, 0, ',', '.') }}</span>
                    @php $wc = match($wd->status) { 'processed','approved' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                    <span class="{{ $wc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst($wd->status === 'processed' ? 'success' : $wd->status) }}</span>
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
                        @php $wc = match($wd->status) { 'processed','approved' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                        <span class="{{ $wc }} text-xs font-semibold px-2.5 py-1 rounded-full">{{ ucfirst($wd->status === 'processed' ? 'success' : $wd->status) }}</span>
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
