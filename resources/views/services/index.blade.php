@extends('layouts.app')
@section('title', 'Browse Services')
@section('content')

<div class="bg-white min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                @if(request('q'))
                    Results for "{{ request('q') }}"
                @elseif(request('category'))
                    {{ optional($categories->firstWhere('id', request('category')))->name ?? 'Category' }}
                @else
                    All Services
                @endif
                <span class="text-gray-400 text-lg font-normal">({{ $services->total() }} results)</span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            {{-- Search in results --}}
            <form action="{{ route('services.index') }}" method="GET" class="relative">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search services..."
                       class="pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </form>
            {{-- Sort --}}
            <div class="relative" x-data="{ sortOpen: false }">
                <button @click="sortOpen = !sortOpen" class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    Sort by <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div x-show="sortOpen" @click.away="sortOpen = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-30">
                    @foreach([''=>'Latest','rating'=>'Best Rating','orders'=>'Most Orders','price_asc'=>'Price: Low to High','price_desc'=>'Price: High to Low'] as $val => $label)
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
                    <h3 class="text-lg font-bold text-gray-900">Filters</h3>
                    @if(request()->hasAny(['q','category','sort','min_price','max_price','min_rating']))
                        <a href="{{ route('services.index') }}" class="text-sm text-gray-400 hover:text-red-500 transition">
                            <i class="fas fa-filter-circle-xmark"></i>
                        </a>
                    @endif
                </div>

                {{-- Category Filter --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <button @click="filterOpen.cat = !filterOpen.cat" class="flex items-center justify-between w-full text-sm font-semibold text-gray-800 mb-3">
                        Category <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="filterOpen.cat ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.cat" x-transition class="space-y-1">
                        <a href="{{ route('services.index', request()->except('category')) }}"
                           class="block px-3 py-2 rounded-lg text-sm transition {{ !request('category') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            All Categories
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
                        Price Range <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="filterOpen.price ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="filterOpen.price" x-transition>
                        <form method="GET" action="{{ route('services.index') }}" class="space-y-3">
                            @foreach(request()->except(['min_price','max_price']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                           class="w-full pl-7 pr-2 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <span class="text-gray-300">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                                           class="w-full pl-7 pr-2 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-2 bg-blue-600 text-white text-sm rounded-lg font-medium hover:bg-blue-700 transition">Apply</button>
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
                                <span>& Up</span>
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
                <div class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl hover:border-blue-100 transition-all duration-300">
                    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                        @php $cover = $service->coverImage ?? $service->images->first(); @endphp
                        @if($cover)
                            <img src="{{ Storage::url($cover->image_path) }}" alt="{{ $service->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <i class="fas fa-image text-4xl text-gray-300"></i>
                            </div>
                        @endif
                        @auth
                        <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn absolute top-3 right-3 z-10 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center transition shadow-sm cursor-pointer {{ auth()->user()->hasFavorited($service->id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                            <i class="{{ auth()->user()->hasFavorited($service->id) ? 'fas' : 'far' }} fa-heart text-sm"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" class="absolute top-3 right-3 z-10 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 transition shadow-sm">
                            <i class="far fa-heart text-sm"></i>
                        </a>
                        @endauth
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-semibold shrink-0">
                                {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-gray-700 truncate block">{{ $service->provider->name }}</span>
                                @if($service->total_orders >= 50)
                                    <span class="text-[10px] text-yellow-600">Top Rated</span>
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
                    <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between">
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Starting at</span>
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
                    Load More Services
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-20">
                <div class="text-6xl text-gray-200 mb-4"><i class="fas fa-search"></i></div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No services found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your search or filters to find what you need.</p>
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition">
                    Clear Filters
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- Footer --}}
<footer class="bg-white border-t border-gray-200 pt-12 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-10">
            <div class="col-span-2 md:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-blue-600 mb-3">
                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-bold">S</span>
                    ServeMix
                </a>
                <p class="text-sm text-gray-500 leading-relaxed mb-4">Connect with top-tier freelancers and get your projects done faster, better, and more efficiently.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-8 h-8 bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-blue-600 rounded-full flex items-center justify-center transition"><i class="fab fa-twitter text-sm"></i></a>
                    <a href="#" class="w-8 h-8 bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-blue-600 rounded-full flex items-center justify-center transition"><i class="fab fa-facebook-f text-sm"></i></a>
                    <a href="#" class="w-8 h-8 bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-blue-600 rounded-full flex items-center justify-center transition"><i class="fab fa-instagram text-sm"></i></a>
                    <a href="#" class="w-8 h-8 bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-blue-600 rounded-full flex items-center justify-center transition"><i class="fab fa-linkedin-in text-sm"></i></a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Categories</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-blue-600 transition">Graphics & Design</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Digital Marketing</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Writing & Translation</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Video & Animation</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Music & Audio</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Programming & Tech</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">About</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-blue-600 transition">Careers</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Press & News</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Partnerships</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Terms of Service</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Support</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-blue-600 transition">Help & Support</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Trust & Safety</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Selling on ServeMix</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Buying on ServeMix</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-400">
            <span>&copy; {{ date('Y') }} ServeMix International Ltd. All rights reserved.</span>
            <div class="flex items-center gap-4"><span>English</span><span>USD</span></div>
        </div>
    </div>
</footer>

@endsection
