@extends('layouts.app')
@section('title', $provider->name . ' - Profil Provider')

@section('meta_title', $provider->name . ' - ServeMix Provider')
@section('meta_description', Str::limit($provider->profile?->bio ?? 'Checkout my provider profile and services on ServeMix.', 150))
@if($provider->avatar)
    @section('meta_image', url(Storage::url($provider->avatar)))
@endif

@section('content')

<div class="bg-white min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


    {{-- Provider Header --}}
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-3xl p-6 md:p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/[0.06] rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/[0.04] rounded-full"></div>
        <div class="relative flex flex-col sm:flex-row items-center sm:items-start gap-6 pt-4 sm:pt-0">
            {{-- Avatar --}}
            <div class="shrink-0">
                @if($provider->avatar)
                    <img src="{{ Storage::url($provider->avatar) }}" alt="{{ $provider->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white/30">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-white/20 flex items-center justify-center text-3xl font-bold border-4 border-white/30 shadow-lg">
                        {{ strtoupper(substr($provider->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center sm:text-left">
                <div class="flex items-center justify-center sm:justify-start gap-2 mb-1">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ $provider->name }}</h1>
                    @if(optional($provider->profile)->is_verified_provider)
                        <i class="fas fa-check-circle text-blue-400 text-xl" title="Verified Provider"></i>
                    @endif
                </div>
                <p class="text-blue-100 text-sm mb-3">{{ '@' . $provider->username }}</p>

                @if($provider->profile?->bio)
                    <p class="text-blue-50 text-sm leading-relaxed max-w-2xl mb-4">{{ $provider->profile->bio }}</p>
                @endif

                <div class="flex flex-wrap justify-center sm:justify-start gap-4 text-sm">
                    @if($provider->profile?->city || $provider->profile?->country)
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-blue-200"></i>
                            {{ collect([$provider->profile->city, $provider->profile->country])->filter()->join(', ') }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-calendar text-blue-200"></i>
                        Member sejak {{ $stats['member_since']->format('M Y') }}
                    </span>
                    @if($provider->profile?->website)
                        <a href="{{ $provider->profile->website }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-1.5 hover:text-white transition">
                            <i class="fas fa-globe text-blue-200"></i>
                            Website
                        </a>
                    @endif
                </div>
            </div>

            {{-- Contact Button --}}
            @auth
                @if(auth()->id() !== $provider->id)
                    <div class="shrink-0">
                        <form action="{{ route('messages.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="provider_id" value="{{ $provider->id }}">
                            <button type="submit" class="bg-white text-blue-600 px-5 py-2.5 rounded-2xl font-bold text-sm hover:bg-blue-50 transition shadow-sm hover:shadow-lg">
                                <i class="fas fa-envelope mr-1.5"></i> Hubungi
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-100/80 shadow-sm">
            <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_services'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Layanan Aktif</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-100/80 shadow-sm">
            <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_orders'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Order Selesai</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-100/80 shadow-sm">
            <div class="text-2xl font-extrabold text-yellow-600">
                <i class="fas fa-star text-yellow-400 text-lg"></i>
                {{ number_format($stats['avg_rating'], 1) }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Rating Rata-rata</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-100/80 shadow-sm">
            <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_reviews'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Review</div>
        </div>
    </div>

    {{-- Skills & Languages --}}
    @if($provider->profile?->skills || $provider->profile?->languages)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if($provider->profile?->skills && count($provider->profile->skills))
        <div>
            <h3 class="font-semibold text-gray-800 mb-3">
                <i class="fas fa-tools text-blue-500 mr-1.5"></i> Keahlian
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($provider->profile->skills as $skill)
                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif
        @if($provider->profile?->languages && count($provider->profile->languages))
        <div>
            <h3 class="font-semibold text-gray-800 mb-3">
                <i class="fas fa-language text-green-500 mr-1.5"></i> Bahasa
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($provider->profile->languages as $lang)
                    <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm">{{ $lang }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Portfolios --}}
    @if($provider->portfolios && $provider->portfolios->count())
    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-5">
            <i class="fas fa-images text-blue-500 mr-2"></i>Portofolio & Hasil Karya
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($provider->portfolios as $portfolio)
                <div class="group relative rounded-2xl overflow-hidden bg-gray-100 aspect-square cursor-pointer shadow-sm border border-gray-100">
                    @if($portfolio->media_type === 'image')
                        <img src="{{ filter_var($portfolio->media_path, FILTER_VALIDATE_URL) ? $portfolio->media_path : Storage::url($portfolio->media_path) }}" alt="{{ $portfolio->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <video src="{{ filter_var($portfolio->media_path, FILTER_VALIDATE_URL) ? $portfolio->media_path : Storage::url($portfolio->media_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" muted loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-transparent transition">
                            <i class="fas fa-play-circle text-white text-3xl opacity-80"></i>
                        </div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                        <h4 class="text-white font-bold text-sm truncate">{{ $portfolio->title }}</h4>
                        @if($portfolio->description)
                            <p class="text-gray-300 text-xs mt-1 line-clamp-2">{{ $portfolio->description }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Services --}}
    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-5">
            <i class="fas fa-briefcase text-blue-500 mr-2"></i>Layanan ({{ $services->count() }})
        </h2>

        @if($services->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                <div class="group bg-white border border-gray-100/80 rounded-3xl overflow-hidden hover:shadow-xl hover:border-blue-100 transition-all duration-300 shadow-sm">
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
                        @auth
                        <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn absolute top-3 right-3 w-9 h-9 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-gray-500 hover:text-red-500 transition shadow-sm">
                            <i class="{{ auth()->user()->hasFavorited($service->id) ? 'fas text-red-500' : 'far' }} fa-heart text-sm"></i>
                        </button>
                        @endauth
                        @if($service->category)
                            <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur text-xs text-gray-600 px-2.5 py-1 rounded-full">{{ $service->category->name }}</span>
                        @endif
                    </div>
                    <div class="p-4">
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
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Mulai dari</span>
                        <span class="font-bold text-gray-900 text-lg">Rp {{ number_format($service->getLowestPrice(), 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <i class="fas fa-briefcase text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada layanan aktif.</p>
            </div>
        @endif
    </div>

    {{-- Reviews --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-5">
            <i class="fas fa-star text-yellow-400 mr-2"></i>Review Terbaru ({{ $stats['total_reviews'] }})
        </h2>

        @if($reviews->count())
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="bg-white rounded-2xl p-5 border border-gray-100/80 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ strtoupper(substr($review->customer->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-1">
                                <span class="font-semibold text-gray-800 text-sm">{{ $review->customer->name }}</span>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            @if($review->service)
                                <a href="{{ route('services.show', $review->service->slug) }}" class="text-xs text-blue-500 hover:underline mb-2 inline-block">
                                    {{ $review->service->title }}
                                </a>
                            @endif
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $review->comment }}</p>

                            @if($review->provider_reply)
                            <div class="mt-3 ml-4 pl-4 border-l-2 border-blue-200">
                                <p class="text-xs text-blue-600 font-semibold mb-1">Balasan Provider:</p>
                                <p class="text-gray-600 text-sm">{{ $review->provider_reply }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <i class="fas fa-comment-slash text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada review.</p>
            </div>
        @endif
    </div>

</div>
</div>

@auth
@push('scripts')
<script>
function toggleFavorite(serviceId, btn) {
    fetch(`/favorites/${serviceId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    }).then(r => r.json()).then(d => {
        const icon = btn.querySelector('i');
        if (d.favorited) {
            icon.classList.remove('far');
            icon.classList.add('fas', 'text-red-500');
        } else {
            icon.classList.remove('fas', 'text-red-500');
            icon.classList.add('far');
        }
    });
}
</script>
@endpush
@endauth
@endsection
