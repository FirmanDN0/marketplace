@extends('layouts.app')
@section('title', 'Order Detail')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
            @php $sc = match($order->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress','paid' => 'bg-blue-100 text-blue-700', 'cancelled','disputed' => 'bg-red-100 text-red-700', 'delivered' => 'bg-indigo-100 text-indigo-700', default => 'bg-yellow-100 text-yellow-700' }; @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ str_replace('_',' ',$order->status) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Order Info</h3></div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><p class="text-xs text-gray-400 mb-1">Customer</p><p class="text-sm font-medium text-gray-900">{{ optional($order->customer)->name }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Provider</p><p class="text-sm font-medium text-gray-900">{{ optional($order->provider)->name }}</p></div>
                    </div>
                    <div><p class="text-xs text-gray-400 mb-1">Service</p><p class="text-sm font-medium text-gray-900">{{ optional($order->service)->title }}</p></div>
                    <div><p class="text-xs text-gray-400 mb-1">Package</p><p class="text-sm text-gray-700">{{ optional($order->package)->name }} <span class="text-gray-400">({{ optional($order->package)->package_type }})</span></p></div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-gray-100">
                        <div><p class="text-xs text-gray-400 mb-1">Order Price</p><p class="text-sm font-bold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Platform Fee</p><p class="text-sm font-semibold text-orange-600">Rp {{ number_format($order->platform_fee, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Provider Earns</p><p class="text-sm font-semibold text-green-600">Rp {{ number_format($order->provider_earning, 0, ',', '.') }}</p></div>
                    </div>
                    @if($order->notes)
                        <div class="pt-2 border-t border-gray-100"><p class="text-xs text-gray-400 mb-1">Customer Notes</p><p class="text-sm text-gray-700">{{ $order->notes }}</p></div>
                    @endif
                    @if($order->delivery_message)
                        <div class="pt-2 border-t border-gray-100"><p class="text-xs text-gray-400 mb-1">Delivery Message</p><p class="text-sm text-gray-700">{{ $order->delivery_message }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Payment --}}
            @if($order->payment)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Payment</h3></div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div><p class="text-xs text-gray-400 mb-1">Amount</p><p class="text-sm font-semibold text-gray-900">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Method</p><p class="text-sm text-gray-700">{{ $order->payment->payment_method }}</p></div>
                        <div><p class="text-xs text-gray-400 mb-1">Status</p>
                            @php $psc = match($order->payment->status) { 'paid','settlement','capture' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $psc }}">{{ $order->payment->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Review --}}
            @if($order->review)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Review</h3></div>
                <div class="p-5">
                    <div class="flex items-center gap-1 text-yellow-400 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= $order->review->rating ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700">{{ $order->review->comment }}</p>
                </div>
            </div>
            @endif

            {{-- Dispute --}}
            @if($order->dispute)
            <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-red-100 bg-red-50"><h3 class="font-semibold text-red-700"><i class="fas fa-exclamation-triangle mr-1"></i> Dispute</h3></div>
                <div class="p-5 space-y-2">
                    <p class="text-sm"><span class="font-medium text-gray-700">Reason:</span> {{ $dispute->reason }}</p>
                    <p class="text-sm text-gray-600">{{ $order->dispute->description }}</p>
                    @php $dsc = match($order->dispute->status) { 'resolved','closed' => 'bg-green-100 text-green-700', 'under_review' => 'bg-yellow-100 text-yellow-700', default => 'bg-red-100 text-red-700' }; @endphp
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $dsc }}">{{ str_replace('_',' ',$order->dispute->status) }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div>
            @if(!in_array($order->status, ['completed','cancelled']))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h4 class="font-semibold text-gray-900">Admin Actions</h4></div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}"
                          onsubmit="return confirm('Force cancel this order?')">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cancellation Reason</label>
                            <input type="text" name="reason" placeholder="Reason (required)" required
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> Force Cancel
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
