@extends('layouts.app')
@section('title', 'Review Moderation')
@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Review Moderation</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $totalReviews }} total &middot; {{ $hiddenReviews }} hidden</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari review atau nama user..."
                   class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <select name="status" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="visible" {{ request('status') === 'visible' ? 'selected' : '' }}>Visible</option>
                <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
            </select>
            <select name="rating" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Rating</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                @endfor
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'rating']))
                <a href="{{ route('admin.reviews.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-semibold transition text-center">Reset</a>
            @endif
        </form>
    </div>

    {{-- Reviews --}}
    @if($reviews->count())
    <div class="space-y-4">
        @foreach($reviews as $review)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden {{ !$review->is_visible ? 'opacity-60 border-red-200' : '' }}">
            <div class="p-5">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    {{-- Review Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            {{-- Stars --}}
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            @if(!$review->is_visible)
                                <span class="bg-red-100 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">Hidden</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $review->created_at->format('d M Y H:i') }}</span>
                        </div>

                        <p class="text-sm text-gray-700 mb-3">{{ $review->comment }}</p>

                        @if($review->provider_reply)
                        <div class="bg-gray-50 rounded-lg p-3 text-sm">
                            <span class="text-xs font-semibold text-gray-500">Balasan Provider:</span>
                            <p class="text-gray-600 mt-1">{{ $review->provider_reply }}</p>
                        </div>
                        @endif

                        <div class="flex flex-wrap gap-4 mt-3 text-xs text-gray-500">
                            <span><i class="fas fa-user mr-1"></i> {{ optional($review->customer)->name ?? 'Deleted' }}</span>
                            <span><i class="fas fa-arrow-right mx-1"></i></span>
                            <span><i class="fas fa-store mr-1"></i> {{ optional($review->provider)->name ?? 'Deleted' }}</span>
                            @if($review->service)
                                <a href="{{ route('admin.services.show', $review->service_id) }}" class="text-blue-500 hover:underline">
                                    <i class="fas fa-briefcase mr-1"></i> {{ Str::limit($review->service->title, 30) }}
                                </a>
                            @endif
                            @if($review->order)
                                <a href="{{ route('admin.orders.show', $review->order_id) }}" class="text-blue-500 hover:underline">
                                    <i class="fas fa-box mr-1"></i> #{{ $review->order->order_number }}
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex sm:flex-col gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.reviews.toggle', $review->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full {{ $review->is_visible ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} px-3 py-2 rounded-lg text-xs font-semibold transition">
                                <i class="fas {{ $review->is_visible ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                {{ $review->is_visible ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Yakin hapus review ini? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-lg text-xs font-semibold transition">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $reviews->links() }}</div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
        <i class="fas fa-star text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Tidak ada review ditemukan.</p>
    </div>
    @endif
</div>
@endsection
