@extends('layouts.app')
@section('title', 'Order Confirmation')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('services.show', $service->slug) }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">Confirm Your Order</h1>
    </div>

    {{-- Service & Package Summary --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">{{ $service->title }}</h3></div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400 mb-1">Package</p><p class="text-sm font-medium text-gray-900">{{ ucfirst($package->package_type) }}: {{ $package->name }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Price</p><p class="text-lg font-bold text-blue-600">Rp {{ number_format($package->price, 0, ',', '.') }}</p></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400 mb-1">Delivery Time</p><p class="text-sm text-gray-700">{{ $package->delivery_days }} day{{ $package->delivery_days!=1?'s':'' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Revisions</p><p class="text-sm text-gray-700">{{ $package->revisions == -1 ? 'Unlimited' : $package->revisions }}</p></div>
            </div>
            @if($package->features)
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-2">What's Included</p>
                <ul class="space-y-1.5">
                    @foreach($package->features as $feat)
                        <li class="flex items-center gap-2 text-sm text-gray-700"><i class="fas fa-check text-green-500 text-xs"></i> {{ $feat }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    {{-- Order Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5">
            <form method="POST" action="{{ route('customer.orders.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Additional Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="notes" name="notes" rows="4"
                              placeholder="Any specific requirements for this order…"
                              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('notes') }}</textarea>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                        <i class="fas fa-box"></i> Confirm & Proceed to Payment
                    </button>
                    <a href="{{ route('services.show', $service->slug) }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
