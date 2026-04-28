@extends('layouts.app')
@section('title', 'Detail Layanan')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Layanan</h1>
                <p class="text-gray-500 text-sm">Pratinjau bagaimana layanan Anda terlihat</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('provider.services.edit', $service) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50 text-sm">
                <i class="fas fa-pen"></i> Edit Layanan
            </a>
            @if($service->status === 'active')
                <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition shadow-sm text-sm">
                    <i class="fas fa-external-link-alt"></i> Lihat di Marketplace
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        @php $sc = match($service->status) { 
                            'active' => 'bg-green-100 text-green-700 border-green-200', 
                            'paused' => 'bg-yellow-100 text-yellow-700 border-yellow-200', 
                            'rejected','deleted' => 'bg-red-100 text-red-700 border-red-200', 
                            default => 'bg-gray-100 text-gray-600 border-gray-200' 
                        }; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $sc }}">
                            {{ $service->status }}
                        </span>
                        <div class="text-xs text-gray-400">
                            Dibuat {{ $service->created_at->format('d M Y') }}
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $service->title }}</h2>
                    
                    <div class="flex flex-wrap items-center gap-6 mb-8 py-4 border-y border-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ number_format($service->avg_rating, 1) }}</div>
                                <div class="text-[10px] text-gray-400 uppercase font-bold">Rating</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $service->total_orders }}</div>
                                <div class="text-[10px] text-gray-400 uppercase font-bold">Pesanan</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ optional($service->category)->name }}</div>
                                <div class="text-[10px] text-gray-400 uppercase font-bold">Kategori</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Deskripsi</h3>
                        <div class="text-gray-600 leading-relaxed text-sm space-y-4">
                            {!! nl2br(e($service->description)) !!}
                        </div>
                    </div>

                    @if($service->tags)
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($service->tags as $tag)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Gallery --}}
            @if($service->images->count() > 0)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Galeri Gambar</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($service->images as $img)
                        <div class="relative aspect-video rounded-2xl overflow-hidden border border-gray-100 group">
                            <img src="{{ Storage::url($img->image_path) }}" alt="Service Image" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            @if($img->is_cover)
                                <span class="absolute top-2 left-2 px-2 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-lg shadow-lg">COVER</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar - Packages --}}
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="font-bold text-gray-900">Paket Harga</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($service->packages as $pkg)
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-md 
                                    {{ $pkg->package_type === 'basic' ? 'bg-blue-50 text-blue-600' : 
                                       ($pkg->package_type === 'standard' ? 'bg-purple-50 text-purple-600' : 'bg-amber-50 text-amber-600') }}">
                                    {{ $pkg->package_type }}
                                </span>
                                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                            </div>
                            <h4 class="font-bold text-gray-800 text-sm mb-2">{{ $pkg->name }}</h4>
                            <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $pkg->description }}</p>
                            
                            <div class="flex items-center gap-4 text-[11px] font-medium text-gray-400 mb-4">
                                <span class="flex items-center gap-1.5"><i class="fas fa-clock text-gray-300"></i> {{ $pkg->delivery_days }} Hari</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-redo text-gray-300"></i> {{ $pkg->revisions == -1 ? 'Unlimited' : $pkg->revisions }} Revisi</span>
                            </div>

                            @if($pkg->features)
                            <div class="space-y-2">
                                @foreach($pkg->features as $feature)
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <i class="fas fa-check text-green-500 text-[10px]"></i>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if($service->status === 'rejected')
            <div class="bg-red-50 border border-red-100 rounded-3xl p-6">
                <div class="flex items-center gap-3 mb-3 text-red-700">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <h4 class="font-bold">Layanan Ditolak</h4>
                </div>
                <p class="text-sm text-red-600 mb-4">Layanan Anda memerlukan perbaikan sebelum dapat diaktifkan kembali.</p>
                @if($service->rejection_reason)
                    <div class="bg-white/50 rounded-xl p-4 border border-red-200">
                        <p class="text-xs font-bold text-red-800 uppercase mb-1">Alasan Penolakan:</p>
                        <p class="text-sm text-red-700 italic">"{{ $service->rejection_reason }}"</p>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
