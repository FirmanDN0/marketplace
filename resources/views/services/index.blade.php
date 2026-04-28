@extends('layouts.app')
@section('title', 'Jelajahi Layanan')
@section('content')

<div class="bg-white min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                @if(request('q'))
                    Hasil untuk "{{ request('q') }}"
                @elseif(request('category'))
                    {{ optional($categories->firstWhere('id', request('category')))->name ?? 'Kategori' }}
                @else
                    Semua Layanan
                @endif
                <span class="text-gray-400 text-lg font-normal">({{ $services->total() }} hasil)</span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            {{-- Search in results --}}
            <form action="{{ route('services.index') }}" method="GET" class="relative">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan..."
                       class="pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </form>
            {{-- Sort --}}
            <div class="relative" x-data="{ sortOpen: false }">
                <button @click="sortOpen = !sortOpen" class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    Urutkan <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div x-show="sortOpen" @click.away="sortOpen = false" x-transition
                     class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-30">
                    @foreach(['' => 'Terbaru', 'rating' => 'Rating Terbaik', 'orders' => 'Paling Banyak Dipesan', 'price_asc' => 'Harga: Rendah ke Tinggi', 'price_desc' => 'Harga: Tinggi ke Rendah'] as $val => $label)
                        <a href="{{ route('services.index', array_merge(request()->all(), ['sort' => $val])) }}"
                           class="block px-4 py-2 text-sm {{ request('sort') === $val ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-8">

        {{-- Left Sidebar Filter --}}
        <aside class="w-64 shrink-0 hidden lg:block" x-data="{ filterOpen: { cat: true, price: true, rating: true } }">
            <div class="sticky top-24 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Filter</h3>
                    @if(request()->hasAny(['q','category','sort','min_price','max_price','min_rating']))
                        <a href="{{ route('services.index') }}" class="text-sm text-gray-400 hover:text-red-500 transition" title="Hapus semua filter">
                            <i class="fas fa-filter-circle-xmark"></i>
                        </a>
                    @endif
                </div>

                {{-- Category Filter --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <button @click="filterOpen.cat = !filterOpen.cat" class="flex items-center justify-between w-full text-sm font-semibold text-gray-800 mb-3">
                        Kategori <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="filterOpen.cat ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.cat" x-transition class="space-y-1">
                        <a href="{{ route('services.index', request()->except('category')) }}"
                           class="block px-3 py-2 rounded-lg text-sm transition {{ !request('category') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            Semua Kategori
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $cat->id])) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ request('category') == $cat->id ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ $cat->name }}
                                @if(isset($cat->services_count))
                                    <span class="text-gray-400 text-xs">({{ $cat->services_count }})</span>
                                @endif
                            </a>
                            @foreach($cat->children as $sub)
                                <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $sub->id])) }}"
                                   class="flex items-center justify-between px-3 py-2 pl-7 rounded-lg text-sm transition {{ request('category') == $sub->id ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-500 hover:bg-gray-50' }}">
                                    {{ $sub->name }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                {{-- Price Range --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <button @click="filterOpen.price = !filterOpen.price" class="flex items-center justify-between w-full text-sm font-semibold text-gray-800 mb-3">
                        Rentang Harga <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="filterOpen.price ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.price" x-transition>
                        <form method="GET" action="{{ route('services.index') }}" class="space-y-3">
                            @foreach(request()->except(['min_price','max_price']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                           class="w-full pl-8 pr-2 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <span class="text-gray-300">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maks"
                                           class="w-full pl-8 pr-2 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-2 bg-blue-600 text-white text-sm rounded-lg font-medium hover:bg-blue-700 transition">Terapkan</button>
                        </form>
                    </div>
                </div>

                {{-- Rating Filter --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <button @click="filterOpen.rating = !filterOpen.rating" class="flex items-center justify-between w-full text-sm font-semibold text-gray-800 mb-3">
                        Rating <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="filterOpen.rating ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.rating" x-transition class="space-y-2">
                        @foreach([4,3,2,1] as $val)
                            <a href="{{ route('services.index', array_merge(request()->except('min_rating'), ['min_rating' => request('min_rating') == $val ? null : $val])) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ request('min_rating') == $val ? 'bg-yellow-50 text-yellow-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                <div class="flex gap-0.5">
                                    @for($s = 0; $s < 5; $s++)
                                        <i class="{{ $s < $val ? 'fas' : 'far' }} fa-star text-yellow-400 text-xs"></i>
                                    @endfor
                                </div>
                                <span>& Ke Atas</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            @if($services->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($services as $service)
                <div class="group bg-white border border-gray-100 rounded-2xl overflow-hidden card-hover-lift card-glow">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        @php $cover = $service->coverImage ?? $service->images->first(); @endphp
                        @if($cover)
                            <img src="{{ Storage::url($cover->image_path) }}" alt="{{ $service->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full service-placeholder flex items-center justify-center relative">
                                <div class="text-center">
                                    <i class="fas fa-briefcase text-3xl text-blue-300/60 mb-2"></i>
                                    <p class="text-xs text-blue-400/50 font-medium">Layanan Digital</p>
                                </div>
                            </div>
                        @endif
                        @auth
                        <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn absolute top-3 right-3 z-10 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center transition-all duration-300 shadow-sm cursor-pointer hover:scale-110 {{ auth()->user()->hasFavorited($service->id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                            <i class="{{ auth()->user()->hasFavorited($service->id) ? 'fas' : 'far' }} fa-heart text-sm"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="absolute top-3 right-3 z-10 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:scale-110 transition-all duration-300 shadow-sm">
                            <i class="far fa-heart text-sm"></i>
                        </a>
                        @endauth
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 text-white flex items-center justify-center text-xs font-semibold shrink-0 shadow-sm">
                                {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-gray-700 truncate block">{{ $service->provider->name }}</span>
                                @if($service->total_orders >= 50)
                                    <span class="text-[10px] text-yellow-600"><i class="fas fa-award mr-0.5"></i>Top Rated</span>
                                @endif
                            </div>
                        </div>
                        <h3 class="font-semibold text-gray-900 text-sm mb-3 line-clamp-2 leading-snug min-h-[2.5rem]">
                            <a href="{{ route('services.show', $service->slug) }}" class="hover:text-blue-600 transition">{{ $service->title }}</a>
                        </h3>
                        <div class="flex items-center gap-1.5 text-sm">
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                            <span class="font-semibold text-gray-800">{{ number_format($service->avg_rating, 1) }}</span>
                            <span class="text-gray-400">({{ $service->total_reviews }})</span>
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between bg-gray-50/50">
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Mulai dari</span>
                        <span class="font-bold text-gray-900 text-lg">Rp {{ number_format($service->getLowestPrice(), 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($services->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $services->links() }}
                </div>
            @endif

            {{-- Load More CTA --}}
            @if($services->hasMorePages())
            <div class="mt-6 text-center">
                <a href="{{ $services->nextPageUrl() }}" class="inline-flex items-center px-6 py-3 border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Muat Lebih Banyak
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-20">
                <div class="text-6xl text-gray-200 mb-4"><i class="fas fa-search"></i></div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Layanan tidak ditemukan</h3>
                <p class="text-gray-500 mb-6">Coba sesuaikan pencarian atau filter Anda.</p>
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition">
                    Hapus Filter
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

@include('partials._footer')

@endsection
