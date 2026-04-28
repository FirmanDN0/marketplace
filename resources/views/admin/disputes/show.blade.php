@extends('layouts.app')
@section('title', 'Dispute Detail')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
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
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Resolution Notes (Visible to both)</label>
                            <textarea name="resolution" rows="4" required placeholder="Explain your final decision..."
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-3 pt-2">
                            <button type="submit" name="action" value="refund_customer" class="w-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-3 rounded-xl text-sm font-semibold transition text-left relative overflow-hidden group" onsubmit="return confirm('Are you sure you want to refund the customer?')">
                                <div class="flex items-center gap-3">
                                    <div class="bg-red-100 text-red-600 rounded-lg p-2"><i class="fas fa-undo"></i></div>
                                    <div>
                                        <div class="font-bold">Side with Customer</div>
                                        <div class="text-xs text-red-600/80 font-normal mt-0.5">Cancel order & refund Rp {{ number_format(optional($dispute->order)->price ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </button>

                            <button type="submit" name="action" value="release_to_provider" class="w-full bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 px-4 py-3 rounded-xl text-sm font-semibold transition text-left relative overflow-hidden group" onsubmit="return confirm('Are you sure you want to release funds to the provider?')">
                                <div class="flex items-center gap-3">
                                    <div class="bg-green-100 text-green-600 rounded-lg p-2"><i class="fas fa-check-circle"></i></div>
                                    <div>
                                        <div class="font-bold">Side with Provider</div>
                                        <div class="text-xs text-green-600/80 font-normal mt-0.5">Complete order & release earnings</div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
