@extends('layouts.app')
@section('title', 'Leave Review')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-arrow-left text-lg"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">Leave a Review</h1>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 mb-6">
        <div class="text-xs text-blue-400 uppercase font-medium">Order {{ $order->order_number }}</div>
        <div class="text-sm font-semibold text-blue-700 mt-0.5">{{ optional($order->service)->title }}</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('customer.reviews.store', $order->id) }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Your Rating</label>
                    <div class="grid grid-cols-5 gap-2" x-data="{ rating: {{ old('rating', 0) }} }">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer" :class="rating >= {{ $i }} ? 'ring-2 ring-yellow-400 bg-yellow-50' : 'bg-gray-50 hover:bg-yellow-50'"
                               class="rounded-xl p-3 text-center border border-gray-200 transition">
                            <input type="radio" name="rating" value="{{ $i }}" class="hidden" x-model="rating" {{ old('rating')==$i ? 'checked' : '' }}>
                            <div class="text-yellow-400 text-lg">
                                {!! str_repeat('<i class="fas fa-star"></i>',$i) !!}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $i }} star{{ $i>1?'s':'' }}</div>
                        </label>
                        @endfor
                    </div>
                </div>
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1.5">Your Review <span class="text-gray-400 font-normal">(min 10 characters)</span></label>
                    <textarea id="comment" name="comment" rows="5" required
                              placeholder="Share your experience with this service…"
                              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('comment') }}</textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition inline-flex items-center gap-2">
                        <i class="fas fa-star"></i> Submit Review
                    </button>
                    <a href="{{ route('customer.orders.show', $order->id) }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
