@extends('layouts.app')
@section('title', $service->title)

@section('meta_title', $service->title . ' - ServeMix')
@section('meta_description', Str::limit(strip_tags($service->description), 150))
@php 
    $ogImage = $service->coverImage ?? $service->images->first(); 
@endphp
@if($ogImage)
    @section('meta_image', url(Storage::url($ogImage->image_path)))
@endif

@section('content')

{{-- Hero Header --}}
<section class="hero-gradient relative overflow-hidden py-12 sm:py-16">
    <div class="absolute top-[10%] left-[15%] w-48 h-48 bg-indigo-600/25 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[10%] right-[10%] w-64 h-64 bg-violet-600/20 rounded-full glow-orb animate-float-reverse"></div>
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute inset-0 shimmer-effect opacity-15"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider text-blue-200/50 mb-6 anim-fade-in-down">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors"><i class="fas fa-home text-sm"></i></a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Layanan</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-blue-100/80 truncate max-w-[200px]">{{ $service->title }}</span>
        </nav>

        <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight mb-5 anim-fade-in-up max-w-3xl">{{ $service->title }}</h1>

        {{-- Meta Profile Bar --}}
        <div class="flex flex-wrap items-center gap-5 anim-fade-in-up delay-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-blue-500/30">
                    {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                </div>
                <a href="{{ route('provider.profile', $service->provider->username) }}" class="font-bold text-white hover:text-blue-200 transition-colors text-sm">{{ $service->provider->name }}</a>
            </div>
            <div class="h-4 w-px bg-white/20 hidden sm:block"></div>
            <div class="flex items-center gap-1.5 text-sm">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="font-bold text-white">{{ number_format($service->avg_rating, 1) }}</span>
                <span class="text-blue-200/60 font-medium">({{ $service->total_reviews }} ulasan)</span>
            </div>
            <div class="h-4 w-px bg-white/20 hidden sm:block"></div>
            <span class="text-xs font-bold text-blue-200/50 uppercase tracking-wider">{{ $service->total_orders }} Pesanan Selesai</span>
        </div>
    </div>
</section>

<div class="bg-gray-50/30 min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-col lg:flex-row gap-10">
        {{-- Left Content --}}
        <div class="flex-1 min-w-0 space-y-8">
            {{-- Gallery --}}
            <div x-data="{ activeImg: 0 }">
                @if($service->images->count())
                    <div class="rounded-3xl overflow-hidden bg-gray-100 aspect-video mb-4 shadow-lg border border-gray-100/80">
                        @foreach($service->images as $i => $img)
                            <img x-show="activeImg === {{ $i }}"
                                 src="{{ filter_var($img->image_path, FILTER_VALIDATE_URL) ? $img->image_path : Storage::url($img->image_path) }}"
                                 alt="{{ $service->title }}"
                                 class="w-full h-full object-cover" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
                        @endforeach
                    </div>
                    @if($service->images->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
                        @foreach($service->images as $i => $img)
                            <button @click="activeImg = {{ $i }}"
                                    :class="activeImg === {{ $i }} ? 'ring-2 ring-blue-600 opacity-100 scale-95' : 'opacity-60 hover:opacity-100 hover:scale-95'"
                                    class="w-24 h-16 rounded-xl overflow-hidden shrink-0 transition-all duration-300 border border-gray-100">
                                <img src="{{ filter_var($img->image_path, FILTER_VALIDATE_URL) ? $img->image_path : Storage::url($img->image_path) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                    @endif
                @else
                    <div class="rounded-3xl service-placeholder aspect-video flex items-center justify-center shadow-inner border border-gray-100">
                        <div class="text-center">
                            <i class="fas fa-briefcase text-5xl text-blue-400/50 mb-3"></i>
                            <p class="text-xs text-blue-400/40 font-bold uppercase tracking-widest">Layanan Digital Profesional</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- About This Service --}}
            <div class="bg-white rounded-3xl border border-gray-100/80 p-8 shadow-sm">
                <h2 class="text-lg font-extrabold text-gray-900 mb-5 flex items-center gap-2.5"><i class="fas fa-info-circle text-blue-500 text-base"></i>Tentang Layanan Ini</h2>
                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed font-medium">
                    {!! nl2br(e($service->description)) !!}
                </div>
                @if($service->tags)
                    <div class="flex flex-wrap gap-2.5 mt-8 pt-8 border-t border-gray-100">
                        @foreach($service->tags as $tag)
                            <span class="px-3.5 py-1.5 bg-blue-50/50 text-blue-600 rounded-xl text-xs font-bold uppercase tracking-wider border border-blue-100/50">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Package Comparison Matrix --}}
            @if($service->packages->count() > 1)
            @php
                $allFeatures = [];
                foreach($service->packages as $pkg) {
                    if($pkg->features) {
                        foreach($pkg->features as $f) {
                            if(!in_array($f, $allFeatures)) $allFeatures[] = $f;
                        }
                    }
                }
            @endphp
            <div class="bg-white rounded-3xl border border-gray-100/80 p-8 shadow-sm">
                <h2 class="text-lg font-extrabold text-gray-900 mb-6 flex items-center gap-2.5"><i class="fas fa-columns text-indigo-500 text-base"></i>Bandingkan Paket</h2>
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead>
                            <tr class="border-b-2 border-gray-100">
                                <th class="py-4 px-4 bg-gray-50/50 rounded-tl-xl font-bold text-gray-900 w-1/3 min-w-[200px]">Fitur Spesifik</th>
                                @foreach($service->packages as $pkg)
                                    <th class="py-4 px-4 text-center min-w-[140px]">
                                        <div class="font-bold text-gray-900 mb-1 uppercase tracking-widest text-[10px]">{{ ucfirst($pkg->package_type) }}</div>
                                        <div class="text-blue-600 font-extrabold text-base">Rp {{ number_format($pkg->price, 0, ',', '.') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr><td class="py-4 px-4 font-medium text-gray-900 bg-gray-50/50">Deskripsi Singkat</td>
                                @foreach($service->packages as $pkg)<td class="py-4 px-4 text-center text-xs leading-relaxed">{{ $pkg->name }}</td>@endforeach
                            </tr>
                            <tr><td class="py-4 px-4 font-medium text-gray-900 bg-gray-50/50">Waktu Pengerjaan</td>
                                @foreach($service->packages as $pkg)<td class="py-4 px-4 text-center font-bold text-gray-800">{{ $pkg->delivery_days }} Hari</td>@endforeach
                            </tr>
                            <tr><td class="py-4 px-4 font-medium text-gray-900 bg-gray-50/50">Jumlah Revisi</td>
                                @foreach($service->packages as $pkg)<td class="py-4 px-4 text-center font-bold text-gray-800">{{ $pkg->revisions == -1 ? 'Tak Terbatas' : $pkg->revisions }}</td>@endforeach
                            </tr>
                            @foreach($allFeatures as $feature)
                            <tr><td class="py-4 px-4 font-medium text-gray-900 bg-gray-50/50">{{ $feature }}</td>
                                @foreach($service->packages as $pkg)
                                    <td class="py-4 px-4 text-center">
                                        @if($pkg->features && in_array($feature, $pkg->features))
                                            <i class="fas fa-check text-emerald-500 text-lg"></i>
                                        @else
                                            <i class="fas fa-minus text-gray-300"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                            <tr><td class="py-4 px-4 bg-gray-50/50 rounded-bl-xl"></td>
                                @foreach($service->packages as $pkg)
                                    <td class="py-4 px-4 text-center">
                                        @auth
                                            @if(auth()->user()->isCustomer())
                                                <form method="GET" action="{{ route('customer.orders.create') }}">
                                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl transition-all text-xs shadow-md shadow-blue-500/20 hover:-translate-y-0.5">Pilih</button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="block w-full py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl transition text-xs shadow-sm hover:-translate-y-0.5">Login</a>
                                        @endauth
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- About the Seller --}}
            <div class="bg-white rounded-3xl border border-gray-100/80 p-8 shadow-sm">
                <h2 class="text-lg font-extrabold text-gray-900 mb-6 flex items-center gap-2.5"><i class="fas fa-user-tie text-violet-500 text-base"></i>Tentang Penyedia Jasa</h2>
                <div class="flex flex-col sm:flex-row items-start gap-5 mb-6">
                    <div class="w-18 h-18 rounded-2.5xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-2xl font-bold shrink-0 shadow-lg shadow-blue-500/20">
                        {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <h3 class="font-extrabold text-gray-900 text-base leading-none">{{ $service->provider->name }}</h3>
                            @if(optional($service->provider->profile)->is_verified_provider)
                                <i class="fas fa-check-circle text-blue-500 text-sm" title="Verified Provider"></i>
                            @endif
                        </div>
                        <p class="text-sm text-blue-600 font-bold mb-4">Spesialis {{ optional($service->category)->name }} Profesional</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-sm text-gray-500">
                            <div><div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Dari</div><div class="font-bold text-gray-700 mt-1">{{ $service->provider->profile->country ?? 'Indonesia' }}</div></div>
                            <div><div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Bergabung</div><div class="font-bold text-gray-700 mt-1">{{ $service->provider->created_at->format('Y') }}</div></div>
                            <div><div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Respon Rata-rata</div><div class="font-bold text-gray-700 mt-1">4 jam</div></div>
                            <div><div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Pengiriman Terakhir</div><div class="font-bold text-gray-700 mt-1">1 hari lalu</div></div>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mb-6 font-medium border-t border-gray-50 pt-5">
                    {{ $service->provider->profile->bio ?? 'Penyedia jasa berpengalaman yang berkomitmen menghadirkan kualitas terbaik.' }}
                </p>
                @auth
                    @if(auth()->user()->isCustomer())
                    <form method="POST" action="{{ route('messages.start') }}">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $service->provider_id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 border border-gray-200 hover:border-blue-600 hover:text-blue-600 text-gray-700 text-sm font-bold rounded-2xl transition duration-300 hover:bg-blue-50/50">
                            <i class="fas fa-comment-dots text-base"></i> Hubungi Penyedia Jasa
                        </button>
                    </form>
                    @endif
                @endauth
            </div>

            {{-- Reviews --}}
            <div class="bg-white rounded-3xl border border-gray-100/80 p-8 shadow-sm">
                <h2 class="text-lg font-extrabold text-gray-900 mb-6 flex items-center gap-2.5"><i class="fas fa-comments text-amber-500 text-base"></i>Ulasan Klien ({{ $service->reviews->count() }})</h2>
                <div class="space-y-6">
                    @forelse($service->reviews as $review)
                        <div class="{{ !$loop->last ? 'pb-6 border-b border-gray-100' : '' }}">
                            <div class="flex items-start gap-4 mb-2">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0 border border-indigo-200/50">
                                    {{ strtoupper(substr($review->customer->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-4">
                                        <h4 class="font-bold text-gray-800 text-sm truncate">{{ $review->customer->name }}</h4>
                                        <span class="text-xs text-gray-400 font-semibold shrink-0">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex gap-0.5 my-1.5">
                                        @for($s = 0; $s < 5; $s++)
                                            <i class="{{ $s < $review->rating ? 'fas' : 'far' }} fa-star text-yellow-400 text-xs"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed font-medium">{{ $review->comment }}</p>
                                    @if($review->provider_reply)
                                        <div class="mt-4 pl-4 border-l-3 border-blue-600 bg-blue-50/50 rounded-r-2xl py-3 pr-4">
                                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Balasan Penyedia Jasa</span>
                                            <p class="text-sm text-gray-600 font-medium leading-relaxed">{{ $review->provider_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-comments text-xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-semibold">Belum ada ulasan. Jadilah yang pertama memesan!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="w-full lg:w-96 shrink-0">
            <div class="lg:sticky lg:top-24 space-y-6">

                {{-- Package Card --}}
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-lg shadow-blue-100/30 overflow-hidden" x-data="{ activePkg: 0 }">
                    @if($service->packages->count() > 1)
                    <div class="flex border-b border-gray-100 bg-gray-50/50">
                        @foreach($service->packages as $i => $pkg)
                            <button @click="activePkg = {{ $i }}"
                                    :class="activePkg === {{ $i }} ? 'border-b-2 border-blue-600 text-blue-600 font-bold bg-white' : 'text-gray-400 hover:text-gray-600 font-semibold'"
                                    class="flex-1 py-4 text-xs uppercase tracking-wider text-center transition">
                                {{ ucfirst($pkg->package_type) }}
                            </button>
                        @endforeach
                    </div>
                    @endif

                    @foreach($service->packages as $i => $pkg)
                    <div x-show="activePkg === {{ $i }}" x-transition:enter="transition duration-200" class="p-8">
                        <div class="flex items-baseline justify-between mb-4">
                            <h3 class="font-bold text-gray-800 text-sm">Paket {{ ucfirst($pkg->package_type) }}</h3>
                            <span class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-6 font-semibold leading-relaxed">{{ $pkg->name }}</p>
                        <div class="space-y-3.5 mb-6 border-t border-b border-gray-100 py-5">
                            <div class="flex items-center gap-3 text-sm text-gray-600 font-medium"><i class="far fa-clock text-gray-400 w-4 text-center"></i>{{ $pkg->delivery_days }} Hari Waktu Pengerjaan</div>
                            <div class="flex items-center gap-3 text-sm text-gray-600 font-medium"><i class="fas fa-sync-alt text-gray-400 w-4 text-center text-xs"></i>{{ $pkg->revisions == -1 ? 'Revisi Tidak Terbatas' : $pkg->revisions . ' Kali Revisi' }}</div>
                            @if($pkg->features)
                                @foreach($pkg->features as $f)
                                    <div class="flex items-center gap-3 text-sm text-gray-600 font-medium"><i class="fas fa-check text-emerald-500 w-4 text-center text-xs"></i>{{ $f }}</div>
                                @endforeach
                            @endif
                        </div>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <form method="GET" action="{{ route('customer.orders.create') }}">
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-blue-500/20 hover:-translate-y-0.5 active:translate-y-0">Lanjutkan Pemesanan</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl text-center transition-all duration-300 shadow-lg shadow-blue-500/20">Masuk untuk Memesan</a>
                        @endauth
                        @auth
                            @if(auth()->user()->isCustomer())
                            <form method="POST" action="{{ route('messages.start') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="provider_id" value="{{ $service->provider_id }}">
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <button type="submit" class="w-full py-3.5 border border-gray-200 text-gray-700 font-bold rounded-2xl hover:bg-gray-50 transition text-sm hover:border-blue-200 hover:text-blue-600">
                                    <i class="fas fa-comment-dots mr-1.5"></i> Hubungi Penyedia Jasa
                                </button>
                            </form>
                            @endif
                        @endauth
                    </div>
                    @endforeach
                </div>

                {{-- Protection Badge --}}
                <div class="bg-gradient-to-br from-blue-50/50 to-indigo-50/50 rounded-3xl p-5 flex items-start gap-4 border border-blue-100/50 shadow-sm">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                        <i class="fas fa-shield-alt text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">Perlindungan Klien ServeMix</h4>
                        <p class="text-xs text-gray-500 mt-1 font-medium leading-relaxed">Dana Anda ditahan secara aman di escrow platform dan dilepaskan ke provider hanya setelah Anda memverifikasi pekerjaan.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-center gap-6 text-sm text-gray-400 font-bold uppercase tracking-wider">
                    @auth
                    <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn flex items-center gap-2 transition hover:scale-105 {{ auth()->user()->hasFavorited($service->id) ? 'text-red-500' : 'hover:text-red-500' }}">
                        <i class="{{ auth()->user()->hasFavorited($service->id) ? 'fas' : 'far' }} fa-heart"></i>
                        <span>{{ auth()->user()->hasFavorited($service->id) ? 'Tersimpan' : 'Simpan' }}</span>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-red-500 transition"><i class="far fa-heart"></i> Simpan</a>
                    @endauth
                    <button class="flex items-center gap-2 hover:text-blue-600 transition"><i class="fas fa-share-alt"></i> Bagikan</button>
                    <button class="flex items-center gap-2 hover:text-gray-600 transition"><i class="far fa-flag"></i> Laporkan</button>
                </div>

                {{-- Related Services --}}
                @if($relatedServices->count())
                <div class="bg-white rounded-3xl border border-gray-100/80 p-6 shadow-sm">
                    <h4 class="font-extrabold text-gray-800 text-sm mb-5 flex items-center gap-2"><i class="fas fa-th-large text-blue-500 text-xs"></i>Layanan Terkait</h4>
                    <div class="space-y-4">
                        @foreach($relatedServices->take(3) as $rel)
                            <a href="{{ route('services.show', $rel->slug) }}" class="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-gray-50 transition duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden border border-gray-100">
                                    @if($rel->images->first())
                                        <img src="{{ Storage::url($rel->images->first()->image_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-briefcase text-gray-300 text-sm"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm text-gray-700 font-bold truncate group-hover:text-blue-600 transition-colors duration-200">{{ $rel->title }}</div>
                                    <div class="text-xs text-gray-400 font-bold mt-1">Rp {{ number_format($rel->getLowestPrice(), 0, ',', '.') }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

@endsection
