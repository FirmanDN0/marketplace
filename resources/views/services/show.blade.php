@extends('layouts.app')
@section('title', $service->title)
@section('content')

<div class="bg-white min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('services.index') }}" class="hover:text-blue-600 transition flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Back to Services
        </a>
    </nav>

    {{-- Title --}}
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">{{ $service->title }}</h1>

    {{-- Meta --}}
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">
                {{ strtoupper(substr($service->provider->name, 0, 1)) }}
            </div>
            <a href="{{ route('provider.profile', $service->provider->username) }}" class="font-medium text-gray-800 hover:text-blue-600 transition">{{ $service->provider->name }}</a>
        </div>
        <div class="flex items-center gap-1 text-sm">
            <i class="fas fa-star text-yellow-400"></i>
            <span class="font-semibold text-gray-800">{{ number_format($service->avg_rating, 1) }}</span>
            <span class="text-gray-400">({{ $service->total_reviews }})</span>
        </div>
        <span class="text-sm text-gray-400">{{ $service->total_orders }} Orders in Queue</span>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Left Content --}}
        <div class="flex-1 min-w-0 space-y-8">
            {{-- Gallery --}}
            <div x-data="{ activeImg: 0 }">
                @if($service->images->count())
                    <div class="rounded-2xl overflow-hidden bg-gray-100 aspect-video mb-3">
                        @foreach($service->images as $i => $img)
                            <img x-show="activeImg === {{ $i }}"
                                 src="{{ Storage::url($img->image_path) }}"
                                 alt="{{ $service->title }}"
                                 class="w-full h-full object-cover" x-transition>
                        @endforeach
                    </div>
                    @if($service->images->count() > 1)
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach($service->images as $i => $img)
                            <button @click="activeImg = {{ $i }}"
                                    :class="activeImg === {{ $i }} ? 'ring-2 ring-blue-500' : 'opacity-60 hover:opacity-100'"
                                    class="w-20 h-14 rounded-lg overflow-hidden shrink-0 transition">
                                <img src="{{ Storage::url($img->image_path) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                    @endif
                @else
                    <div class="rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 aspect-video flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-gray-300"></i>
                    </div>
                @endif
            </div>

            {{-- About This Service --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">About This Service</h2>
                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                    {!! nl2br(e($service->description)) !!}
                </div>
                @if($service->tags)
                    <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-gray-100">
                        @foreach($service->tags as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- About the Seller --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">About The Seller</h2>
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold shrink-0">
                        {{ strtoupper(substr($service->provider->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $service->provider->name }}</h3>
                        <p class="text-sm text-blue-600 mb-1">Professional {{ optional($service->category)->name }} Specialist</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm text-gray-500 mt-3">
                            <div>
                                <div class="text-xs text-gray-400">From</div>
                                <div class="font-medium text-gray-700">{{ $service->provider->profile->country ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Member since</div>
                                <div class="font-medium text-gray-700">{{ $service->provider->created_at->format('Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Avg. Response</div>
                                <div class="font-medium text-gray-700">4 hours</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Last Delivery</div>
                                <div class="font-medium text-gray-700">1 day ago</div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    {{ $service->provider->profile->bio ?? 'Experienced professional providing high-quality work.' }}
                </p>
                @auth
                    @if(auth()->user()->isCustomer())
                    <form method="POST" action="{{ route('messages.start') }}">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $service->provider_id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="text-blue-600 hover:text-blue-700 text-sm font-semibold transition">
                            Contact Me
                        </button>
                    </form>
                    @endif
                @endauth
            </div>

            {{-- Reviews --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Reviews ({{ $service->reviews->count() }})</h2>
                <div class="space-y-6">
                    @forelse($service->reviews as $review)
                        <div class="{{ !$loop->last ? 'pb-6 border-b border-gray-100' : '' }}">
                            <div class="flex items-start gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-{{ ['red','blue','green','purple','orange','teal'][crc32($review->customer->name) % 6] }}-100 text-{{ ['red','blue','green','purple','orange','teal'][crc32($review->customer->name) % 6] }}-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($review->customer->name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-800 text-sm">{{ $review->customer->name }}</h4>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex gap-0.5 my-1">
                                        @for($s = 0; $s < 5; $s++)
                                            <i class="{{ $s < $review->rating ? 'fas' : 'far' }} fa-star text-yellow-400 text-xs"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                                    @if($review->provider_reply)
                                        <div class="mt-3 pl-4 border-l-2 border-blue-200 bg-blue-50/50 rounded-r-lg py-2 pr-3">
                                            <span class="text-xs font-semibold text-blue-600">Seller's Response</span>
                                            <p class="text-sm text-gray-600 mt-1">{{ $review->provider_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="text-4xl text-gray-200 mb-3"><i class="fas fa-comments"></i></div>
                            <p class="text-gray-500 text-sm">No reviews yet. Be the first to order!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Sidebar: Sticky Package --}}
        <div class="w-full lg:w-96 shrink-0">
            <div class="sticky top-24 space-y-4">

                {{-- Package Card --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ activePkg: 0 }">
                    {{-- Package Tabs --}}
                    @if($service->packages->count() > 1)
                    <div class="flex border-b border-gray-200">
                        @foreach($service->packages as $i => $pkg)
                            <button @click="activePkg = {{ $i }}"
                                    :class="activePkg === {{ $i }} ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                                    class="flex-1 py-3 text-sm text-center transition">
                                {{ ucfirst($pkg->package_type) }}
                            </button>
                        @endforeach
                    </div>
                    @endif

                    {{-- Package Content --}}
                    @foreach($service->packages as $i => $pkg)
                    <div x-show="activePkg === {{ $i }}" x-transition class="p-6">
                        <div class="flex items-baseline justify-between mb-3">
                            <h3 class="font-semibold text-gray-800">{{ ucfirst($pkg->package_type) }} Package</h3>
                            <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4 leading-relaxed">{{ $pkg->name }}</p>

                        <div class="space-y-2.5 mb-5">
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="far fa-clock text-gray-400 w-4 text-center"></i>
                                {{ $pkg->delivery_days }} Days Delivery
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="fas fa-sync-alt text-gray-400 w-4 text-center text-xs"></i>
                                {{ $pkg->revisions == -1 ? 'Unlimited' : $pkg->revisions }} Revisions
                            </div>
                            @if($pkg->features)
                                @foreach($pkg->features as $f)
                                    <div class="flex items-center gap-3 text-sm text-gray-600">
                                        <i class="fas fa-check text-green-500 w-4 text-center text-xs"></i>
                                        {{ $f }}
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        @auth
                            @if(auth()->user()->isCustomer())
                                <form method="GET" action="{{ route('customer.orders.create') }}">
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                                        Continue (Rp {{ number_format($pkg->price, 0, ',', '.') }})
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition text-center shadow-lg shadow-blue-200/50">
                                Sign in to Order
                            </a>
                        @endauth

                        @auth
                            @if(auth()->user()->isCustomer())
                            <form method="POST" action="{{ route('messages.start') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="provider_id" value="{{ $service->provider_id }}">
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <button type="submit" class="w-full py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                                    Contact Seller
                                </button>
                            </form>
                            @endif
                        @endauth
                    </div>
                    @endforeach
                </div>

                {{-- Protection Badge --}}
                <div class="bg-gray-50 rounded-2xl p-4 flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">ServeMix Protection</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Your payment is held securely until you approve the work.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-center gap-6 text-sm text-gray-400">
                    @auth
                    <button onclick="toggleFavorite({{ $service->id }}, this)" class="favorite-btn flex items-center gap-1.5 transition {{ auth()->user()->hasFavorited($service->id) ? 'text-red-500' : 'hover:text-red-500' }}">
                        <i class="{{ auth()->user()->hasFavorited($service->id) ? 'fas' : 'far' }} fa-heart"></i>
                        <span>{{ auth()->user()->hasFavorited($service->id) ? 'Saved' : 'Save' }}</span>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center gap-1.5 hover:text-red-500 transition"><i class="far fa-heart"></i> Save</a>
                    @endauth
                    <button class="flex items-center gap-1.5 hover:text-blue-600 transition"><i class="fas fa-share-alt"></i> Share</button>
                    <button class="flex items-center gap-1.5 hover:text-gray-600 transition"><i class="far fa-flag"></i> Report</button>
                </div>

                {{-- Related Services --}}
                @if($relatedServices->count())
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h4 class="font-semibold text-gray-800 text-sm mb-4">Related Services</h4>
                    <div class="space-y-3">
                        @foreach($relatedServices->take(3) as $rel)
                            <a href="{{ route('services.show', $rel->slug) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition group">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                    @if($rel->thumbnail)
                                        <img src="{{ asset('storage/'.$rel->thumbnail) }}" class="w-full h-full rounded-lg object-cover">
                                    @else
                                        <i class="fas fa-image text-gray-300 text-sm"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm text-gray-700 font-medium truncate group-hover:text-blue-600 transition">{{ $rel->title }}</div>
                                    <div class="text-xs text-gray-400">Rp {{ number_format($rel->getLowestPrice(), 0, ',', '.') }}</div>
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
