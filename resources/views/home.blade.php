@extends('layouts.app')
@section('title', 'ServeMix - Temukan Layanan Profesional Terbaik')
@section('content')

{{-- Hero Section - Animated Gradient with Floating Shapes --}}
<section class="relative hero-gradient overflow-hidden min-h-[620px] flex items-center py-24 sm:py-32">
    {{-- Ambient light orbs --}}
    <div class="absolute top-[10%] left-[20%] w-72 h-72 bg-indigo-600/35 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[15%] right-[15%] w-96 h-96 bg-violet-600/30 rounded-full glow-orb animate-float-reverse"></div>
    
    {{-- Floating decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-32 -left-32 w-[500px] h-[500px] bg-white/[0.02] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/4 right-[15%] w-20 h-20 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute bottom-1/4 left-[10%] w-14 h-14 bg-white/[0.03] rounded-full float-shape-reverse"></div>
        {{-- Shimmer overlay --}}
        <div class="absolute inset-0 shimmer-effect opacity-30"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center w-full z-10">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-blue-200 text-xs font-bold uppercase tracking-widest mb-6 anim-fade-in-down">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
            </span>
            Platform Digital 2026
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-7.5xl font-extrabold text-white leading-[1.1] mb-6 tracking-tight anim-fade-in-up">
            Temukan Layanan <span class="text-gradient">Freelance</span><br>
            Unggulan untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-300 to-white">Bisnis Anda</span>
        </h1>
        <p class="text-blue-100/80 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-10 anim-fade-in-up delay-100 leading-relaxed">
            Berkolaborasi dengan talenta profesional terverifikasi untuk mempercepat hasil kerja Anda dengan garansi keamanan transaksi.
        </p>
        
        {{-- Glassmorphic Search Bar --}}
        <form action="{{ route('services.index') }}" method="GET" class="max-w-2xl mx-auto mb-10 anim-fade-in-up delay-200">
            <div class="flex flex-col sm:flex-row bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-2 gap-2 shadow-2xl focus-within:ring-4 focus-within:ring-indigo-500/20 transition-all duration-300">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-white/60"></i>
                    <input type="text" name="q" placeholder="Layanan profesional apa yang Anda butuhkan?"
                           value="{{ request('q') }}"
                           class="w-full pl-12 pr-4 py-4 rounded-2xl text-white placeholder-white/50 text-sm bg-transparent border-0 focus:outline-none focus:ring-0">
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-8 py-4 rounded-2xl font-bold text-sm transition duration-300 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 pulse-glow">
                    Cari Jasa
                </button>
            </div>
        </form>

        <div class="flex flex-wrap justify-center items-center gap-2.5 text-sm anim-fade-in delay-300">
            <span class="text-blue-200/80 font-medium">Populer:</span>
            @foreach($categories->take(5) as $cat)
                <a href="{{ route('services.index', ['category' => $cat->id]) }}"
                   class="px-4 py-1.5 bg-white/5 hover:bg-white/15 border border-white/10 hover:border-white/20 text-white rounded-xl transition duration-300 backdrop-blur-md text-xs font-semibold hover:scale-105 active:scale-95">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Trust Stats --}}
        <div class="grid grid-cols-3 max-w-lg mx-auto mt-16 pt-8 border-t border-white/10 anim-fade-in-up delay-400">
            @php $trustStats = [
                ['value' => '10K+', 'label' => 'Proyek Selesai'],
                ['value' => '5K+', 'label' => 'Talenta Ahli'],
                ['value' => '99%', 'label' => 'Rasio Kepuasan'],
            ]; @endphp
            @foreach($trustStats as $stat)
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $stat['value'] }}</div>
                <div class="text-blue-200/60 text-xs font-semibold uppercase tracking-wider mt-1.5">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Popular Categories --}}
<section class="py-24 bg-white" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight" x-show="shown" x-transition.duration.500ms>Kategori Populer</h2>
                <p class="text-gray-500 text-sm mt-2 font-medium" x-show="shown" x-transition.duration.500ms.delay.100ms>Jelajahi keahlian terbaik berdasarkan kategori favorit Anda</p>
            </div>
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-sm transition-all duration-300 group">
                Lihat Semua Kategori <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        @php
            $catIcons = ['fa-laptop-code','fa-palette','fa-chart-line','fa-pen-nib','fa-film','fa-music','fa-briefcase','fa-mobile-alt'];
            $catGradients = [
                'from-blue-500 to-indigo-600', 'from-pink-500 to-rose-600', 'from-emerald-500 to-green-600', 'from-purple-500 to-violet-600',
                'from-orange-500 to-amber-600', 'from-cyan-500 to-blue-600', 'from-teal-500 to-cyan-600', 'from-violet-500 to-fuchsia-600'
            ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            @foreach($categories->take(8) as $i => $cat)
                <a href="{{ route('services.index', ['category' => $cat->id]) }}"
                   class="category-card card-glow group p-6 bg-gray-50/50 hover:bg-white border border-gray-100 rounded-3xl text-center relative overflow-hidden"
                   x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($i * 70) }}ms'">
                    <div class="category-icon w-14 h-14 bg-gradient-to-br {{ $catGradients[$i % count($catGradients)] }} rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-indigo-600/10">
                        <i class="fas {{ $catIcons[$i % count($catIcons)] }} text-lg text-white"></i>
                    </div>
                    <div class="font-bold text-gray-800 text-sm mb-1.5 transition-colors group-hover:text-blue-600">{{ $cat->name }}</div>
                    <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider">
                        {{ $cat->services_count ?? $cat->services()->count() }} Layanan
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Services --}}
<section class="py-24 bg-gray-50/40" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Layanan Unggulan</h2>
                <p class="text-gray-500 mt-2 text-sm font-medium">Layanan dengan rating tertinggi yang paling diminati oleh klien</p>
            </div>
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-sm transition-all duration-300 group">
                Jelajahi Semua Jasa <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        @if($featuredServices->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredServices->take(4) as $idx => $service)
            <div class="group bg-white rounded-3xl overflow-hidden border border-gray-100/80 card-hover-lift card-glow"
                 x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($idx * 100) }}ms'">
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-50">
                    @php $cover = $service->coverImage ?? $service->images->first(); @endphp
                    @if($cover)
                        <img src="{{ filter_var($cover->image_path, FILTER_VALIDATE_URL) ? $cover->image_path : Storage::url($cover->image_path) }}" alt="{{ $service->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full service-placeholder flex items-center justify-center relative">
                            <div class="text-center">
                                <i class="fas fa-briefcase text-3xl text-blue-400/50 mb-2"></i>
                                <p class="text-xs text-blue-400/40 font-semibold uppercase tracking-wider">Layanan Digital</p>
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
                    <div class="flex items-center gap-2.5 mb-3.5">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-sm shrink-0">
                            {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                        </div>
                        <span class="flex items-center text-sm font-semibold text-gray-600 truncate">
                            {{ $service->provider->name }}
                            <x-verified-badge :user="$service->provider" />
                        </span>
                        @if($service->total_orders >= 50)
                            <span class="ml-auto text-[10px] bg-amber-50 text-amber-700 border border-amber-200/50 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider"><i class="fas fa-award mr-0.5"></i>Top</span>
                        @endif
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
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Mulai Dari</span>
                    <span class="font-extrabold text-gray-900 text-base">Rp {{ number_format($service->getLowestPrice(), 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-3xl border border-gray-100">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-briefcase text-2xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada layanan tersedia</h3>
            <p class="text-gray-400 text-sm mb-6 max-w-sm mx-auto">Jadilah yang pertama menawarkan layanan digital profesional Anda di platform kami.</p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm transition">Mulai Bergabung</a>
            @endguest
        </div>
        @endif
    </div>
</section>

{{-- How It Works --}}
<section class="py-24 hero-gradient relative overflow-hidden" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="absolute top-[10%] left-[5%] w-60 h-60 bg-indigo-500/10 rounded-full glow-orb float-shape"></div>
    <div class="absolute bottom-[10%] right-[5%] w-80 h-80 bg-violet-500/10 rounded-full glow-orb float-shape-reverse"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <h2 class="text-3xl font-extrabold text-white mb-2 tracking-tight" x-show="shown" x-transition.duration.500ms>Cara Kerjanya</h2>
        <p class="text-blue-200/80 mb-16 text-sm font-semibold uppercase tracking-widest" x-show="shown" x-transition.duration.500ms.delay.100ms>Selesaikan Proyek Anda Dalam 3 Langkah Mudah</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            @php $steps = [
                ['icon' => 'fa-search', 'num' => '01', 'title' => 'Temukan Layanan', 'desc' => 'Jelajahi katalog digital terpercaya kami untuk mencari jasa yang pas dengan kebutuhan proyek Anda.'],
                ['icon' => 'fa-clipboard-check', 'num' => '02', 'title' => 'Pesan & Beri Brief', 'desc' => 'Tentukan paket pilihan Anda, ajukan instruksi pekerjaan, dan mulailah berkolaborasi.'],
                ['icon' => 'fa-star', 'num' => '03', 'title' => 'Terima Hasil Kerja', 'desc' => 'Verifikasi file hasil kerja Anda. Jika telah sesuai, berikan rating dan selesaikan pesanan.'],
            ]; @endphp
            @foreach($steps as $si => $step)
            <div class="flex flex-col items-center relative group"
                 x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($si * 120 + 200) }}ms'">
                <div class="relative mb-6">
                    <div class="w-22 h-22 bg-white/10 backdrop-blur-md rounded-2.5xl flex items-center justify-center border border-white/10 shadow-xl transition-transform duration-300 group-hover:scale-105">
                        <i class="fas {{ $step['icon'] }} text-2xl text-white"></i>
                    </div>
                    <span class="absolute -top-2 -right-2 w-7 h-7 bg-white text-indigo-950 rounded-full text-xs font-extrabold flex items-center justify-center shadow-md">{{ $step['num'] }}</span>
                </div>
                <h3 class="font-bold text-white text-lg mb-2.5">{{ $step['title'] }}</h3>
                <p class="text-blue-200/60 text-sm leading-relaxed max-w-xs">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="py-24 bg-gradient-to-b from-white to-blue-50/20" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2 tracking-tight" x-show="shown" x-transition.duration.500ms>Dipercaya oleh Klien & Kreatif</h2>
            <p class="text-gray-500 text-sm font-medium" x-show="shown" x-transition.duration.500ms.delay.100ms>Apa kata mereka yang telah menggunakan platform kami</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php $testimonials = [
                ['text' => '"Hasil desain luar biasa! Pengerjaan diselesaikan lebih awal dan komunikasi dengan penyedia jasa sangat lancar."', 'name' => 'Ahmad R.', 'role' => 'Business Director', 'time' => '2 hari lalu', 'color' => 'bg-red-50 text-red-600 border-red-100/50'],
                ['text' => '"Platform yang sangat membantu startup kami untuk mencari developer andal dengan portofolio yang terpercaya."', 'name' => 'Sari T.', 'role' => 'Content Specialist', 'time' => '1 minggu lalu', 'color' => 'bg-blue-55 text-blue-600 border-blue-100/50'],
                ['text' => '"Sistem escrow/perlindungan pembayarannya membuat saya merasa sangat aman memesan pekerjaan berharga tinggi."', 'name' => 'Budi S.', 'role' => 'Tech Founder', 'time' => '2 minggu lalu', 'color' => 'bg-purple-50 text-purple-600 border-purple-100/50'],
            ]; @endphp
            @foreach($testimonials as $ti => $t)
            <div class="testimonial-card bg-white border border-gray-100 rounded-3xl p-8"
                 x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($ti * 100 + 200) }}ms'">
                <div class="flex gap-0.5 mb-5">
                    @for($s = 0; $s < 5; $s++)
                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6 italic">{{ $t['text'] }}</p>
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-full {{ $t['color'] }} border flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($t['name'], 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-gray-800 text-sm">{{ $t['name'] }}</div>
                        <div class="text-xs text-gray-400 font-semibold mt-0.5">{{ $t['role'] }} · {{ $t['time'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-24" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="hero-gradient rounded-3.5xl overflow-hidden relative"
             x-show="shown" x-transition.duration.700ms>
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-80 h-80 bg-white/[0.03] rounded-full -translate-y-1/2 translate-x-1/4 float-shape-slow"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/[0.02] rounded-full translate-y-1/3 -translate-x-1/4 float-shape"></div>
            </div>
            <div class="relative grid md:grid-cols-2 gap-10 items-center p-8 sm:p-12 md:p-16 z-10">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 tracking-tight">Siap Kembangkan Bisnis Anda?</h2>
                    <p class="text-blue-100/80 mb-8 text-base leading-relaxed">Daftar sekarang untuk menemukan talent profesional tepercaya atau tawarkan keahlian Anda di ServeMix.</p>
                    <div class="flex flex-wrap gap-3.5">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3.5 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition duration-300 hover:shadow-lg active:scale-95 text-sm">
                            <i class="fas fa-compass mr-2"></i> Cari Layanan
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3.5 border-2 border-white/20 hover:border-white text-white font-bold rounded-2xl hover:bg-white/10 transition duration-300 text-sm">
                            <i class="fas fa-user-plus mr-2"></i> Gabung Jadi Seller
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/5 backdrop-blur-md rounded-3xl p-8 border border-white/10 shadow-2xl">
                        <div class="grid grid-cols-2 gap-8 text-center">
                            <div>
                                <div class="text-3xl font-extrabold text-white mb-1.5 tracking-tight">24/7</div>
                                <div class="text-blue-200/60 text-xs font-bold uppercase tracking-wider">Layanan Bantuan</div>
                            </div>
                            <div>
                                <div class="text-3xl font-extrabold text-white mb-1.5 tracking-tight">100%</div>
                                <div class="text-blue-200/60 text-xs font-bold uppercase tracking-wider">Garansi Transaksi</div>
                            </div>
                            <div>
                                <div class="text-3xl font-extrabold text-white mb-1.5 tracking-tight">3 Hari</div>
                                <div class="text-blue-200/60 text-xs font-bold uppercase tracking-wider">Rerata Pengiriman</div>
                            </div>
                            <div>
                                <div class="text-3xl font-extrabold text-white mb-1.5 tracking-tight">Rp 0</div>
                                <div class="text-blue-200/60 text-xs font-bold uppercase tracking-wider">Biaya Mulai</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials._footer')

@endsection
