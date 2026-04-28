@extends('layouts.app')
@section('title', 'ServeMix - Temukan Layanan Profesional')
@section('content')

{{-- Hero Section - Animated Gradient with Floating Shapes --}}
<section class="relative hero-gradient overflow-hidden min-h-[520px] flex items-center">
    {{-- Floating decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-white/[0.04] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-32 -left-32 w-[500px] h-[500px] bg-white/[0.03] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/4 right-1/4 w-24 h-24 bg-white/[0.06] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute bottom-1/3 left-1/5 w-16 h-16 bg-white/[0.05] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/2 right-[10%] w-10 h-10 bg-blue-400/[0.15] rounded-lg rotate-45 float-shape"></div>
        {{-- Shimmer overlay --}}
        <div class="absolute inset-0 shimmer-effect"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center w-full">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-5 anim-fade-in-up">
            Temukan layanan <span class="italic text-gradient">freelance</span><br>
            terbaik untuk<br>
            <span class="text-white">bisnis Anda</span>
        </h1>
        <p class="text-blue-100 text-lg md:text-xl max-w-2xl mx-auto mb-8 anim-fade-in-up delay-200">
            Bekerja sama dengan talenta profesional untuk hasil terbaik dengan biaya efisien
        </p>
        <form action="{{ route('services.index') }}" method="GET" class="max-w-2xl mx-auto mb-8 anim-fade-in-up delay-300">
            <div class="flex bg-white rounded-full shadow-2xl overflow-hidden p-1.5 focus-within:ring-4 focus-within:ring-white/30 transition-all duration-300">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="q" placeholder="Layanan apa yang Anda cari?"
                           value="{{ request('q') }}"
                           class="w-full pl-11 pr-4 py-3.5 rounded-full text-gray-800 text-sm focus:outline-none">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-full font-semibold text-sm transition-all duration-300 active:scale-95 shadow-lg shadow-blue-800/30 pulse-glow">
                    Cari
                </button>
            </div>
        </form>
        <div class="flex flex-wrap justify-center items-center gap-2 text-sm anim-fade-in delay-500">
            <span class="text-blue-200">Populer:</span>
            @foreach($categories->take(5) as $cat)
                <a href="{{ route('services.index', ['category' => $cat->id]) }}"
                   class="px-4 py-1.5 bg-white/10 hover:bg-white/25 border border-white/20 text-white rounded-full transition-all duration-300 backdrop-blur-md text-xs font-medium hover:scale-105">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Trust Stats --}}
        <div class="grid grid-cols-3 max-w-lg mx-auto mt-12 anim-fade-in-up delay-700">
            @php $trustStats = [
                ['value' => '10K+', 'label' => 'Layanan Aktif'],
                ['value' => '5K+', 'label' => 'Freelancer'],
                ['value' => '99%', 'label' => 'Kepuasan'],
            ]; @endphp
            @foreach($trustStats as $stat)
            <div class="text-center stat-number">
                <div class="text-2xl md:text-3xl font-bold text-white">{{ $stat['value'] }}</div>
                <div class="text-blue-200 text-xs mt-1">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Popular Categories --}}
<section class="py-16 bg-white" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900" x-show="shown" x-transition.duration.500ms>Kategori Populer</h2>
                <p class="text-gray-500 text-sm mt-1" x-show="shown" x-transition.duration.500ms.delay.100ms>Jelajahi layanan berdasarkan kategori favorit</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1.5 transition group">
                Lihat Semua <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        @php
            $catIcons = ['fa-laptop-code','fa-palette','fa-chart-line','fa-pen-nib','fa-film','fa-music','fa-briefcase','fa-mobile-alt','fa-shopping-cart','fa-globe','fa-envelope','fa-handshake'];
            $catGradients = [
                'from-blue-500 to-blue-600', 'from-pink-500 to-rose-600', 'from-emerald-500 to-green-600', 'from-purple-500 to-violet-600',
                'from-orange-500 to-amber-600', 'from-indigo-500 to-blue-600', 'from-teal-500 to-cyan-600', 'from-cyan-500 to-sky-600'
            ];
            $catBgs = ['bg-blue-50','bg-pink-50','bg-emerald-50','bg-purple-50','bg-orange-50','bg-indigo-50','bg-teal-50','bg-cyan-50'];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
            @foreach($categories->take(8) as $i => $cat)
                <a href="{{ route('services.index', ['category' => $cat->id]) }}"
                   class="category-card card-glow group p-6 bg-white border border-gray-100 rounded-2xl text-center relative overflow-hidden"
                   x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($i * 80) }}ms'">
                    <div class="category-icon w-16 h-16 bg-gradient-to-br {{ $catGradients[$i % count($catGradients)] }} rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-200/30">
                        <i class="fas {{ $catIcons[$i % count($catIcons)] }} text-xl text-white"></i>
                    </div>
                    <div class="font-semibold text-gray-800 text-sm mb-1">{{ $cat->name }}</div>
                    <div class="text-xs text-gray-400">
                        {{ $cat->services_count ?? $cat->services()->count() }} Layanan
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Services --}}
<section class="py-16 bg-gray-50" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Layanan Unggulan</h2>
                <p class="text-gray-500 mt-1 text-sm">Layanan paling populer dan terbaik untuk Anda</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1.5 transition group">
                Jelajahi semua <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        @if($featuredServices->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredServices->take(4) as $idx => $service)
            <div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 card-hover-lift card-glow"
                 x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($idx * 100) }}ms'">
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
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 text-white flex items-center justify-center text-xs font-semibold shadow-sm">
                            {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ $service->provider->name }}</span>
                        @if($service->total_orders >= 50)
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded font-medium"><i class="fas fa-award text-[10px] mr-0.5"></i>Top</span>
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
                <div class="px-4 py-3 border-t border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Mulai dari</span>
                    <span class="font-bold text-gray-900 text-lg">Rp {{ number_format($service->getLowestPrice(), 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
            <div class="text-5xl text-gray-200 mb-4"><i class="fas fa-tools"></i></div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum ada layanan</h3>
            <p class="text-gray-500 mb-6">Jadilah yang pertama menawarkan layanan profesional Anda.</p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200/50">Mulai Gratis</a>
            @endguest
        </div>
        @endif
    </div>
</section>

{{-- How It Works --}}
<section class="py-20 hero-gradient relative overflow-hidden" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white/[0.03] rounded-full float-shape"></div>
        <div class="absolute bottom-10 right-10 w-20 h-20 bg-white/[0.04] rounded-xl rotate-12 float-shape-reverse"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2" x-show="shown" x-transition.duration.500ms>Cara Kerjanya</h2>
        <p class="text-blue-200 mb-14 text-sm" x-show="shown" x-transition.duration.500ms.delay.100ms>Selesaikan proyek Anda dalam 3 langkah mudah</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            @php $steps = [
                ['icon' => 'fa-search', 'num' => '01', 'title' => 'Temukan Layanan', 'desc' => 'Jelajahi marketplace kami untuk menemukan layanan yang sesuai dengan kebutuhan dan anggaran Anda.'],
                ['icon' => 'fa-clipboard-check', 'num' => '02', 'title' => 'Pesan & Kirim Brief', 'desc' => 'Berikan detail proyek dan persyaratan Anda kepada penyedia jasa untuk memulai.'],
                ['icon' => 'fa-star', 'num' => '03', 'title' => 'Terima Hasil', 'desc' => 'Terima proyek yang telah selesai, review hasilnya, dan lepaskan pembayaran.'],
            ]; @endphp
            @foreach($steps as $si => $step)
            <div class="step-connector flex flex-col items-center relative"
                 x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($si * 150 + 200) }}ms'">
                <div class="relative mb-5">
                    <div class="w-20 h-20 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shadow-lg">
                        <i class="fas {{ $step['icon'] }} text-2xl text-white"></i>
                    </div>
                    <span class="absolute -top-2 -right-2 w-7 h-7 bg-white text-blue-700 rounded-full text-xs font-bold flex items-center justify-center shadow-md">{{ $step['num'] }}</span>
                </div>
                <h3 class="font-bold text-white text-lg mb-2">{{ $step['title'] }}</h3>
                <p class="text-blue-200 text-sm leading-relaxed max-w-xs">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="py-16 bg-gradient-to-b from-white to-blue-50/30" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2" x-show="shown" x-transition.duration.500ms>Dipercaya oleh Ribuan Pengguna</h2>
            <p class="text-gray-500" x-show="shown" x-transition.duration.500ms.delay.100ms>Lihat apa kata komunitas kami</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $testimonials = [
                ['text' => '"Hasil kerja luar biasa! Diselesaikan lebih cepat dari jadwal dan persis seperti yang saya butuhkan."', 'name' => 'Ahmad R.', 'role' => 'Pemilik Bisnis', 'time' => '2 hari lalu', 'color' => 'bg-red-100 text-red-600'],
                ['text' => '"Komunikasi sangat baik dan hasil yang solid. Pasti akan order lagi."', 'name' => 'Sari T.', 'role' => 'Content Creator', 'time' => '1 minggu lalu', 'color' => 'bg-blue-100 text-blue-600'],
                ['text' => '"Profesional, terampil, dan mudah diajak bekerja sama. Sangat direkomendasikan."', 'name' => 'Budi S.', 'role' => 'Startup Founder', 'time' => '2 minggu lalu', 'color' => 'bg-purple-100 text-purple-600'],
            ]; @endphp
            @foreach($testimonials as $ti => $t)
            <div class="testimonial-card bg-white border border-gray-100 rounded-2xl p-7"
                 x-show="shown" x-transition.duration.500ms :style="'transition-delay: {{ ($ti * 120 + 200) }}ms'">
                <div class="flex gap-0.5 mb-4">
                    @for($s = 0; $s < 5; $s++)
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $t['text'] }}</p>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 {{ $t['color'] }} rounded-full flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($t['name'], 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">{{ $t['name'] }}</div>
                        <div class="text-xs text-gray-400">{{ $t['role'] }} · {{ $t['time'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-16" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="hero-gradient rounded-3xl overflow-hidden relative"
             x-show="shown" x-transition.duration.700ms>
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-80 h-80 bg-white/[0.04] rounded-full -translate-y-1/2 translate-x-1/4 float-shape-slow"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/[0.03] rounded-full translate-y-1/3 -translate-x-1/4 float-shape"></div>
            </div>
            <div class="relative grid md:grid-cols-2 gap-8 items-center p-10 md:p-14">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap untuk memulai?</h2>
                    <p class="text-blue-100 mb-8 text-lg">Bergabunglah dengan komunitas profesional dan bisnis berbakat kami hari ini.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-full hover:bg-blue-50 transition shadow-lg hover:shadow-xl hover:scale-105 duration-300">
                            <i class="fas fa-compass mr-2"></i> Jelajahi Layanan
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-full hover:bg-white/15 transition duration-300">
                            <i class="fas fa-user-plus mr-2"></i> Jadi Penyedia Jasa
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div>
                                <div class="text-3xl font-bold text-white mb-1">24/7</div>
                                <div class="text-blue-200 text-xs">Dukungan Pelanggan</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-white mb-1">100%</div>
                                <div class="text-blue-200 text-xs">Pembayaran Aman</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-white mb-1">3 Hari</div>
                                <div class="text-blue-200 text-xs">Rata-rata Pengerjaan</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-white mb-1">0%</div>
                                <div class="text-blue-200 text-xs">Biaya Pendaftaran</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Scroll Reveal Script --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
        if (reveals.length === 0) return;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        reveals.forEach(el => observer.observe(el));
    });
</script>
@endpush

@include('partials._footer')

@endsection
