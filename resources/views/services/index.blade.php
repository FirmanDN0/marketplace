@extends('layouts.app')
@section('title', 'Jelajahi Layanan')
@section('content')

{{-- Hero Header --}}
<section class="hero-gradient relative overflow-hidden py-16 sm:py-20">
    {{-- Ambient glow orbs --}}
    <div class="absolute top-[10%] left-[15%] w-60 h-60 bg-indigo-600/25 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[10%] right-[10%] w-72 h-72 bg-violet-600/20 rounded-full glow-orb animate-float-reverse"></div>

    {{-- Floating shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white/[0.02] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/3 right-[8%] w-12 h-12 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute inset-0 shimmer-effect opacity-15"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="text-center anim-fade-in-up">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">
                @if(request('q'))
                    Hasil untuk "<span class="text-gradient">{{ request('q') }}</span>"
                @elseif(request('category'))
                    {{ optional($categories->firstWhere('id', request('category')))->name ?? 'Kategori' }}
                @else
                    Jelajahi Semua <span class="text-gradient">Layanan</span>
                @endif
            </h1>
            <p class="text-blue-200/60 text-sm font-medium">
                {{ $services->total() }} layanan profesional tersedia untuk Anda
            </p>
        </div>

        {{-- Glassmorphic Search Bar --}}
        <div class="max-w-2xl mx-auto mt-8 anim-fade-in-up delay-100">
            <form action="{{ route('services.index') }}" method="GET">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <div class="flex flex-col sm:flex-row bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-2 gap-2 shadow-2xl focus-within:ring-4 focus-within:ring-indigo-500/20 transition-all duration-300">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-white/60"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan profesional..."
                               class="w-full pl-12 pr-4 py-4 rounded-2xl text-white placeholder-white/50 text-sm bg-transparent border-0 focus:outline-none focus:ring-0">
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-8 py-4 rounded-2xl font-bold text-sm transition duration-300 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0">
                        Cari Jasa
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="bg-gray-50/30 min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Sort Bar --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            @if(request()->hasAny(['q','category','sort','min_price','max_price','min_rating']))
                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-500 hover:bg-red-100 rounded-xl text-xs font-bold transition-colors" title="Hapus semua filter">
                    <i class="fas fa-filter-circle-xmark"></i> Bersihkan Filter
                </a>
            @endif
        </div>
        <div class="relative" x-data="{ sortOpen: false }">
            <button @click="sortOpen = !sortOpen" class="flex items-center gap-2.5 px-5 py-3 bg-white border border-gray-200/80 rounded-2xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-sort text-gray-400 text-xs"></i>Urutkan <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="sortOpen ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="sortOpen" @click.away="sortOpen = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-30">
                @foreach(['' => 'Terbaru', 'rating' => 'Rating Terbaik', 'orders' => 'Paling Banyak Dipesan', 'price_asc' => 'Harga: Rendah ke Tinggi', 'price_desc' => 'Harga: Tinggi ke Rendah'] as $val => $label)
                    <a href="{{ route('services.index', array_merge(request()->all(), ['sort' => $val])) }}"
                       class="block px-4 py-2.5 text-sm font-semibold {{ request('sort') === $val ? 'text-blue-600 bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Left Sidebar Filter --}}
        <aside class="w-full lg:w-68 shrink-0" x-data="{ filterOpen: { cat: true, price: true, rating: true } }">
            <div class="lg:sticky lg:top-24 space-y-5">

                {{-- Category Filter --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100/80">
                    <button @click="filterOpen.cat = !filterOpen.cat" class="flex items-center justify-between w-full text-sm font-bold text-gray-800 mb-4">
                        <span class="flex items-center gap-2"><i class="fas fa-tags text-blue-500 text-xs"></i> Kategori</span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="filterOpen.cat ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.cat" x-transition class="space-y-1 max-h-80 overflow-y-auto pr-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                        <a href="{{ route('services.index', request()->except('category')) }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ !request('category') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/15' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-layer-group text-xs {{ !request('category') ? 'text-blue-200' : 'text-gray-400' }}"></i> Semua Kategori
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $cat->id])) }}"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('category') == $cat->id ? 'bg-blue-600 text-white shadow-md shadow-blue-500/15' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ $cat->name }}
                                @if(isset($cat->services_count))
                                    <span class="{{ request('category') == $cat->id ? 'text-blue-200' : 'text-gray-400' }} text-xs font-semibold">({{ $cat->services_count }})</span>
                                @endif
                            </a>
                            @foreach($cat->children as $sub)
                                <a href="{{ route('services.index', array_merge(request()->except('category'), ['category' => $sub->id])) }}"
                                   class="flex items-center justify-between px-3.5 py-2.5 pl-9 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('category') == $sub->id ? 'bg-blue-600 text-white shadow-md shadow-blue-500/15' : 'text-gray-500 hover:bg-gray-50' }}">
                                    {{ $sub->name }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                {{-- Price Range --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100/80">
                    <button @click="filterOpen.price = !filterOpen.price" class="flex items-center justify-between w-full text-sm font-bold text-gray-800 mb-4">
                        <span class="flex items-center gap-2"><i class="fas fa-money-bill-wave text-emerald-500 text-xs"></i> Rentang Harga</span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="filterOpen.price ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.price" x-transition>
                        <form method="GET" action="{{ route('services.index') }}" class="space-y-4">
                            @foreach(request()->except(['min_price','max_price']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                           class="w-full pl-9 pr-2 py-3 bg-gray-50 border border-gray-200/80 rounded-xl text-xs focus:outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 focus:bg-white transition-all">
                                </div>
                                <span class="text-gray-300 font-bold">—</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maks"
                                           class="w-full pl-9 pr-2 py-3 bg-gray-50 border border-gray-200/80 rounded-xl text-xs focus:outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 focus:bg-white transition-all">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm rounded-xl font-bold transition duration-300 shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0">Terapkan</button>
                        </form>
                    </div>
                </div>

                {{-- Rating Filter --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100/80">
                    <button @click="filterOpen.rating = !filterOpen.rating" class="flex items-center justify-between w-full text-sm font-bold text-gray-800 mb-4">
                        <span class="flex items-center gap-2"><i class="fas fa-star text-yellow-400 text-xs"></i> Rating Layanan</span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="filterOpen.rating ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.rating" x-transition class="space-y-1">
                        @foreach([4,3,2,1] as $val)
                            <a href="{{ route('services.index', array_merge(request()->except('min_rating'), ['min_rating' => request('min_rating') == $val ? null : $val])) }}"
                               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('min_rating') == $val ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'text-gray-600 hover:bg-gray-50' }}">
                                <div class="flex gap-0.5">
                                    @for($s = 0; $s < 5; $s++)
                                        <i class="{{ $s < $val ? 'fas' : 'far' }} fa-star text-yellow-400 text-xs"></i>
                                    @endfor
                                </div>
                                <span class="text-xs">& Ke Atas</span>
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
                <div class="group bg-white border border-gray-100/80 rounded-3xl overflow-hidden card-hover-lift card-glow">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        @php $cover = $service->coverImage ?? $service->images->first(); @endphp
                        @if($cover)
                            <img src="{{ filter_var($cover->image_path, FILTER_VALIDATE_URL) ? $cover->image_path : Storage::url($cover->image_path) }}" alt="{{ $service->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full service-placeholder flex items-center justify-center relative">
                                <div class="text-center">
                                    <i class="fas fa-briefcase text-3xl text-blue-300/60 mb-2"></i>
                                    <p class="text-[10px] text-blue-400/50 font-bold uppercase tracking-wider">Layanan Digital</p>
                                </div>
                            </div>
                        @endif
                        @auth
                        <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn absolute top-3.5 right-3.5 z-10 w-9.5 h-9.5 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center transition shadow-sm cursor-pointer hover:scale-110 {{ auth()->user()->hasFavorited($service->id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                            <i class="{{ auth()->user()->hasFavorited($service->id) ? 'fas' : 'far' }} fa-heart text-sm"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="absolute top-3.5 right-3.5 z-10 w-9.5 h-9.5 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:scale-110 transition shadow-sm">
                            <i class="far fa-heart text-sm"></i>
                        </a>
                        @endauth
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                                {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="flex items-center gap-1 text-sm font-bold text-gray-700 truncate block leading-none">
                                    {{ $service->provider->name }}
                                    <x-verified-badge :user="$service->provider" />
                                </span>
                                @if($service->total_orders >= 50)
                                    <span class="text-[9px] text-amber-600 font-bold uppercase tracking-wider block mt-0.5"><i class="fas fa-award mr-0.5"></i>Top Rated</span>
                                @endif
                            </div>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm mb-3.5 line-clamp-2 leading-snug min-h-[2.5rem]">
                            <a href="{{ route('services.show', $service->slug) }}" class="hover:text-blue-600 transition">{{ $service->title }}</a>
                        </h3>
                        <div class="flex items-center gap-1.5 text-sm">
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                            <span class="font-bold text-gray-800">{{ number_format($service->avg_rating, 1) }}</span>
                            <span class="text-gray-400 font-medium">({{ $service->total_reviews }})</span>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-50 flex items-center justify-between bg-gray-50/40">
                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Mulai dari</span>
                        <span class="font-extrabold text-gray-900 text-base">Rp {{ number_format($service->getLowestPrice(), 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($services->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $services->links() }}
                </div>
            @endif

            {{-- Load More CTA --}}
            @if($services->hasMorePages())
            <div class="mt-8 text-center">
                <a href="{{ $services->nextPageUrl() }}" class="inline-flex items-center px-8 py-3.5 bg-white border border-gray-200/80 rounded-2xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 duration-300">
                    <i class="fas fa-arrow-down mr-2 text-gray-400"></i>Muat Lebih Banyak
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-24 bg-white rounded-3xl border border-gray-100/80 shadow-sm">
                <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-search text-3xl text-blue-300"></i>
                </div>
                <h3 class="text-xl font-extrabold text-gray-800 mb-2 tracking-tight">Layanan tidak ditemukan</h3>
                <p class="text-gray-500 mb-8 max-w-sm mx-auto text-sm font-medium">Coba sesuaikan pencarian Anda atau hapus filter untuk melihat katalog lengkap.</p>
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-2xl font-bold text-sm transition-all duration-300 shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-filter-circle-xmark mr-2"></i>Hapus Semua Filter
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

@include('partials._footer')

@endsection
