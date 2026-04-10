@extends('layouts.app')
@section('title', 'Withdrawals')
@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Withdrawals</h1>
            <p class="text-sm text-gray-500 mt-1">Available Balance: <span class="font-bold text-green-600">Rp {{ number_format($profile->balance, 0, ',', '.') }}</span></p>
        </div>
        @if($profile->balance >= 10)
            <a href="{{ route('provider.withdraw.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2 self-start sm:self-auto">
                <i class="fas fa-plus text-xs"></i> Request Withdrawal
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Mobile Card View --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($withdrawals as $w)
            <div class="px-4 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-900">Rp {{ number_format($w->amount, 0, ',', '.') }}</span>
                    @php $sc = match($w->status) { 'approved','processed' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ $w->status }}</span>
                </div>
                <div class="text-sm text-gray-600">{{ str_replace('_',' ',$w->method) }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $w->created_at->format('M d, Y') }}</div>
                @if($w->notes)
                    <div class="text-xs text-gray-500 mt-1">{{ $w->notes }}</div>
                @endif
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-400">No withdrawal requests.</div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Amount</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Method</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Date</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($withdrawals as $w)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 font-semibold text-gray-900">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ str_replace('_',' ',$w->method) }}</td>
                    <td class="px-5 py-3">
                        @php $sc = match($w->status) { 'approved','processed' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ $w->status }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ $w->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ $w->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No withdrawal requests.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $withdrawals->links() }}</div>
        @endif
    </div>
</div>
@endsection
