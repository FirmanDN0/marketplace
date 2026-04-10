@extends('layouts.app')
@section('title', 'Dispute Detail')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.disputes.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Dispute #{{ $dispute->id }}</h1>
            @php $sc = match($dispute->status) { 'resolved','closed' => 'bg-green-100 text-green-700', 'under_review' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ str_replace('_',' ',$dispute->status) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Dispute Details</h3></div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><p class="text-xs text-gray-400 mb-1">Order</p><p class="text-sm font-medium text-gray-900">{{ optional($dispute->order)->order_number }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Opened By</p><p class="text-sm font-medium text-gray-900">{{ optional($dispute->opener)->name }}</p></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><p class="text-xs text-gray-400 mb-1">Customer</p><p class="text-sm text-gray-700">{{ optional($dispute->order->customer)->name }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Provider</p><p class="text-sm text-gray-700">{{ optional($dispute->order->provider)->name }}</p></div>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-400 mb-1">Reason</p>
                        <p class="text-sm font-medium text-gray-900">{{ $dispute->reason }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Description</p>
                        <p class="text-sm text-gray-700">{{ $dispute->description }}</p>
                    </div>
                    @if($dispute->resolution)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-green-800 mb-1">Resolution</p>
                        <p class="text-sm text-green-700">{{ $dispute->resolution }}</p>
                        <p class="text-xs text-green-500 mt-2">Resolved by {{ optional($dispute->resolver)->name }} &bull; {{ optional($dispute->resolved_at)->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            @if(in_array($dispute->status, ['open','under_review']))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Resolve Dispute</h4></div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.disputes.resolve', $dispute->id) }}" class="space-y-4">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Resolution</label>
                            <textarea name="resolution" rows="5" required placeholder="Describe the resolution decision…"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Close as</label>
                            <select name="status" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        @if($dispute->order && $dispute->order->payment && $dispute->order->payment->status === 'success' && !in_array($dispute->order->status, ['cancelled', 'completed']))
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3">
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="refund" value="1" class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-sm text-yellow-800"><strong>Refund ke customer</strong> — Rp {{ number_format($dispute->order->price, 0, ',', '.') }} akan dikembalikan ke saldo customer dan order dibatalkan.</span>
                            </label>
                        </div>
                        @endif
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Submit Resolution
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
