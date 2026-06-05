@extends('layouts.app')
@section('title', 'Favorit Saya')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-rose-500/20">
                <i class="fas fa-heart text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Favorit Saya</h1>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Layanan yang kamu simpan</p>
            </div>
        </div>
        <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-bold flex items-center gap-1.5 transition">
            Jelajahi Layanan <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    @if($favorites->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($favorites as $service)
        <div class="group bg-white rounded-3xl overflow-hidden border border-gray-100/80 hover:shadow-xl hover:border-blue-100 transition-all duration-300 card-hover-lift">
            <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                @php $cover = $service->coverImage ?? $service->images->first(); @endphp
                @if($cover)
                    <img src="{{ filter_var($cover->image_path, FILTER_VALIDATE_URL) ? $cover->image_path : Storage::url($cover->image_path) }}" alt="{{ $service->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                        <i class="fas fa-image text-4xl text-gray-300"></i>
                    </div>
                @endif
                <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn absolute top-3 right-3 z-10 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-red-500 transition shadow-sm cursor-pointer">
                    <i class="fas fa-heart text-sm"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                    </div>
                    <span class="flex items-center text-sm font-medium text-gray-700">
                        {{ $service->provider->name }}
                        <x-verified-badge :user="$service->provider" />
                    </span>
                </div>
                <h3 class="font-semibold text-gray-900 text-sm mb-3 line-clamp-2 leading-snug min-h-[2.5rem]">
                    <a href="{{ route('services.show', $service->slug) }}" class="hover:text-blue-600 transition">{{ $service->title }}</a>
                </h3>
                <div class="flex items-center gap-1 text-sm mb-3">
                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                    <span class="font-semibold text-gray-800">{{ number_format($service->avg_rating, 1) }}</span>
                    <span class="text-gray-400">({{ $service->total_reviews }})</span>
                </div>
            </div>
            <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Starting at</span>
                <span class="font-bold text-gray-900 text-lg">Rp {{ number_format($service->getLowestPrice(), 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>

    @if($favorites->hasPages())
    <div class="mt-8">{{ $favorites->links() }}</div>
    @endif

    @else
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 py-16 text-center">
        <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="far fa-heart text-2xl text-rose-300"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-2">Belum ada favorit</h3>
        <p class="text-gray-400 text-sm mb-5 font-medium">Tekan tombol <i class="far fa-heart text-red-400"></i> pada layanan untuk menyimpannya di sini.</p>
        <a href="{{ route('services.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15">Jelajahi Layanan</a>
    </div>
    @endif

</div>
@endsection
