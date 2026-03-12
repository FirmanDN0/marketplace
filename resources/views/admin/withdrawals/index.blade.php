@extends('layouts.app')
@section('title', 'Admin: Withdrawals')
@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Withdrawal Requests</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" class="rounded-xl border border-gray-200 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','rejected','processed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Provider</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Amount</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Method</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($withdrawals as $w)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ optional($w->provider)->name }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-900">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ str_replace('_',' ',$w->method) }}</td>
                    <td class="px-5 py-3">
                        @php $sc = match($w->status) { 'approved','processed' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ $w->status }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $w->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3">
                        @if($w->status === 'pending')
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.withdrawals.approve', $w->id) }}"
                                  onsubmit="return confirm('Approve this withdrawal?')">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition"><i class="fas fa-check mr-1"></i>Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.withdrawals.reject', $w->id) }}"
                                  onsubmit="return confirm('Reject this withdrawal?')" class="flex items-center gap-1.5">
                                @csrf
                                <input type="text" name="notes" placeholder="Reason…" required
                                       class="w-28 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-red-400 focus:border-transparent">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition"><i class="fas fa-times mr-1"></i>Reject</button>
                            </form>
                        </div>
                        @else
                            <span class="text-gray-400">&mdash;</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No withdrawal requests.</td></tr>
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
