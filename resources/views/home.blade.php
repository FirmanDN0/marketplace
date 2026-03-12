@extends('layouts.app')
@section('title', 'ServeMix - Find Professional Services')
@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-white rounded-full"></div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-white rounded-full"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4">
            Find the perfect <span class="italic text-blue-200">freelance</span><br>
            services for your<br>
            <span class="text-white">business</span>
        </h1>
        <p class="text-blue-100 text-lg md:text-xl max-w-2xl mx-auto mb-8">
            Work with talented people to get the most out of your time and cost
        </p>
        <form action="{{ route('services.index') }}" method="GET" class="max-w-2xl mx-auto mb-6">
            <div class="flex bg-white rounded-full shadow-xl overflow-hidden p-1.5">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="q" placeholder="What service are you looking for?"
                           value="{{ request('q') }}"
                           class="w-full pl-11 pr-4 py-3.5 rounded-full text-gray-800 text-sm focus:outline-none">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-full font-semibold text-sm transition shadow-lg shadow-blue-300/50">
                    Search
                </button>
            </div>
        </form>
        <div class="flex flex-wrap justify-center gap-2 text-sm">
            <span class="text-blue-200">Popular:</span>
            @foreach($categories->take(5) as $cat)
                <a href="{{ route('services.index', ['category' => $cat->id]) }}"
                   class="px-3 py-1 bg-white/15 hover:bg-white/25 text-white rounded-full transition backdrop-blur-sm text-sm">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Popular Categories --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Popular Categories</h2>
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1 transition">
                View All <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        @php
            $catIcons = ['fa-laptop-code','fa-palette','fa-chart-line','fa-pen-nib','fa-film','fa-music','fa-briefcase','fa-mobile-alt','fa-shopping-cart','fa-globe','fa-envelope','fa-handshake'];
            $catColors = ['bg-blue-50 text-blue-600','bg-pink-50 text-pink-600','bg-green-50 text-green-600','bg-purple-50 text-purple-600','bg-orange-50 text-orange-600','bg-indigo-50 text-indigo-600','bg-teal-50 text-teal-600','bg-cyan-50 text-cyan-600'];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($categories->take(8) as $i => $cat)
                <a href="{{ route('services.index', ['category' => $cat->id]) }}"
                   class="group p-6 bg-white border border-gray-100 rounded-2xl hover:shadow-lg hover:border-blue-200 transition-all duration-300 text-center">
                    <div class="w-14 h-14 {{ $catColors[$i % count($catColors)] }} rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas {{ $catIcons[$i % count($catIcons)] }} text-xl"></i>
                    </div>
                    <div class="font-semibold text-gray-800 text-sm mb-1">{{ $cat->name }}</div>
                    <div class="text-xs text-gray-400">
                        {{ $cat->services_count ?? $cat->services()->count() }} Services
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Services --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Featured Services</h2>
                <p class="text-gray-500 mt-1">Most viewed and top-rated services for you</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1 transition">
                Browse all <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if($featuredServices->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredServices->take(4) as $service)
            <div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:border-blue-100 transition-all duration-300">
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
                        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-semibold">
                            {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ $service->provider->name }}</span>
                        @if($service->total_orders >= 50)
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded font-medium">Top Rated</span>
                        @endif
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
        @else
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
            <div class="text-5xl text-gray-200 mb-4"><i class="fas fa-tools"></i></div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No services yet</h3>
            <p class="text-gray-500 mb-6">Be the first to list your professional service.</p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition">Get Started Free</a>
            @endguest
        </div>
        @endif
    </div>
</section>

{{-- How It Works --}}
<section class="py-16 bg-blue-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">How It Works</h2>
        <p class="text-blue-100 mb-12">Get your project done in 3 easy steps</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php $steps = [
                ['icon' => 'fa-search', 'title' => '1. Find a Service', 'desc' => 'Browse our marketplace to find a service that matches your needs and budget.'],
                ['icon' => 'fa-clipboard-check', 'title' => '2. Place Your Order', 'desc' => 'Provide your project details and requirements to the seller to get started.'],
                ['icon' => 'fa-star', 'title' => '3. Get Results', 'desc' => 'Receive your completed project, review the work, and release payment.'],
            ]; @endphp
            @foreach($steps as $step)
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
                    <i class="fas {{ $step['icon'] }} text-2xl text-white"></i>
                </div>
                <h3 class="font-bold text-white text-lg mb-2">{{ $step['title'] }}</h3>
                <p class="text-blue-100 text-sm leading-relaxed max-w-xs">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-2">Trusted by freelancers and businesses</h2>
        <p class="text-gray-500 text-center mb-12">See what our community has to say</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $testimonials = [
                ['text' => '"Absolutely fantastic work! Delivered ahead of schedule and exactly what I needed."', 'name' => 'Alex M.', 'time' => '2 days ago', 'color' => 'bg-red-100 text-red-600'],
                ['text' => '"Great communication and solid result. Would hire again."', 'name' => 'Jessica T.', 'time' => '1 week ago', 'color' => 'bg-blue-100 text-blue-600'],
                ['text' => '"Professional, skilled, and easy to work with. Highly recommended."', 'name' => 'Michael B.', 'time' => '2 weeks ago', 'color' => 'bg-purple-100 text-purple-600'],
            ]; @endphp
            @foreach($testimonials as $t)
            <div class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition">
                <div class="flex gap-0.5 mb-4">
                    @for($s = 0; $s < 5; $s++)
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $t['text'] }}</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 {{ $t['color'] }} rounded-full flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($t['name'], 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">{{ $t['name'] }}</div>
                        <div class="text-xs text-gray-400">{{ $t['time'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-3xl overflow-hidden relative">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-80 h-80 bg-white rounded-full -translate-y-1/2 translate-x-1/4"></div>
            </div>
            <div class="relative grid md:grid-cols-2 gap-8 items-center p-10 md:p-14">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to get started?</h2>
                    <p class="text-blue-100 mb-8 text-lg">Join our community of talented professionals and businesses today.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-full hover:bg-blue-50 transition shadow-lg">
                            Explore Services
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-full hover:bg-white/10 transition">
                            Become a Seller
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 text-center">
                        <div class="text-5xl text-white/80 mb-4"><i class="fas fa-rocket"></i></div>
                        <p class="text-white/90 font-medium">Start your freelance journey today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <li><a href="#" class="hover:text-blue-600 transition">Intellectual Property Claims</a></li>
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
            <div class="flex items-center gap-4">
                <span>English</span>
                <span>USD</span>
            </div>
        </div>
    </div>
</footer>

@endsection

