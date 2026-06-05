@extends('layouts.checkout')
@section('title', 'Checkout')
@section('content')

{{-- Mobile Step Indicator --}}
<div class="sm:hidden flex items-center justify-center gap-2 mb-6">
    <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center ring-4 ring-blue-100">1</div>
    <span class="text-sm font-bold text-blue-600">Konfirmasi Pesanan</span>
</div>

<form method="POST" action="{{ route('customer.orders.store') }}" id="checkout-form">
    @csrf
    <input type="hidden" name="package_id" value="{{ $package->id }}">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- LEFT COLUMN: Details --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Service Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-shopping-bag text-blue-600"></i>
                    <h2 class="font-bold text-gray-900">Detail Pesanan</h2>
                </div>
                <div class="p-6">
                    {{-- Service Info --}}
                    <div class="flex gap-4 mb-5">
                        @if($service->thumbnail)
                            <img src="{{ Storage::url($service->thumbnail) }}" alt="{{ $service->title }}" class="w-20 h-20 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-briefcase text-blue-400 text-xl"></i>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-900 text-base leading-snug mb-1">{{ $service->title }}</h3>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>oleh</span>
                                <span class="font-semibold text-gray-700">{{ $service->provider->name }}</span>
                                @if($service->provider->is_verified)
                                    <i class="fas fa-check-circle text-blue-500 text-xs"></i>
                                @endif
                            </div>
                            @if($service->avg_rating > 0)
                            <div class="flex items-center gap-1 mt-1">
                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                <span class="text-xs font-semibold text-gray-600">{{ number_format($service->avg_rating, 1) }}</span>
                                <span class="text-xs text-gray-400">({{ $service->total_reviews }} ulasan)</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Package Details --}}
                    <div class="bg-blue-50/60 rounded-xl border border-blue-100 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Paket {{ ucfirst($package->package_type) }}</span>
                                <h4 class="font-bold text-gray-900 text-sm">{{ $package->name }}</h4>
                            </div>
                            <span class="text-lg font-bold text-blue-600">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <div class="w-7 h-7 rounded-lg bg-white border border-blue-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-blue-500 text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-medium">Pengerjaan</div>
                                    <div class="font-semibold text-gray-800 text-xs">{{ $package->delivery_days }} hari</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <div class="w-7 h-7 rounded-lg bg-white border border-blue-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-sync-alt text-blue-500 text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-medium">Revisi</div>
                                    <div class="font-semibold text-gray-800 text-xs">{{ $package->revisions == -1 ? 'Unlimited' : $package->revisions . ' kali' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($package->features)
                        <div class="border-t border-blue-100 pt-3">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Yang Termasuk</p>
                            <ul class="space-y-1.5">
                                @foreach($package->features as $feat)
                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-green-500 text-xs mt-0.5 flex-shrink-0"></i>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notes & Voucher --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-edit text-gray-500"></i>
                    <h2 class="font-bold text-gray-900">Catatan & Voucher</h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan Tambahan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea id="notes" name="notes" rows="3"
                                  placeholder="Tuliskan pesan atau instruksi awal untuk penyedia jasa…"
                                  class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Voucher --}}
                    <div>
                        <label for="voucher_code" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            <i class="fas fa-ticket-alt text-orange-500 mr-1"></i> Kode Voucher
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="voucher_code" name="voucher_code" value="{{ old('voucher_code') }}"
                                   placeholder="Masukkan kode promo"
                                   class="flex-1 rounded-xl border border-gray-200 px-4 py-3 text-sm uppercase focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        @error('voucher_code')
                            <p class="text-red-500 text-xs mt-1.5 font-semibold flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1.5">Diskon akan otomatis dihitung setelah Anda menekan tombol bayar.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Order Summary (Sticky) --}}
        <div class="lg:col-span-2">
            <div class="lg:sticky lg:top-24 space-y-4">
                {{-- Order Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <i class="fas fa-receipt text-green-600"></i>
                        <h2 class="font-bold text-gray-900">Ringkasan Pembayaran</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal Jasa</span>
                            <span class="font-medium text-gray-800">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Biaya Layanan <span class="text-gray-400">(10%)</span></span>
                            <span class="font-medium text-gray-800">Rp {{ number_format($package->price * 0.10, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Diskon Voucher</span>
                            <span class="font-medium text-gray-400">—</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 mt-1">
                            <div class="flex justify-between items-baseline">
                                <span class="font-bold text-gray-900">Estimasi Total</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ number_format($package->price + ($package->price * 0.10), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-2xl font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-lock text-xs"></i> Lanjut ke Pembayaran
                </button>

                {{-- Cancel --}}
                <a href="{{ route('services.show', $service->slug) }}" class="w-full flex items-center justify-center gap-2 text-gray-500 hover:text-gray-700 text-sm font-medium transition py-2">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali ke halaman layanan
                </a>

                {{-- Trust Badges --}}
                <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-3">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-alt text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-xs">Dana Terlindungi</p>
                            <p class="text-[11px] text-gray-400">Uang Anda aman di rekening bersama hingga pekerjaan selesai.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-undo text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-xs">Jaminan Refund</p>
                            <p class="text-[11px] text-gray-400">Ajukan pengembalian dana jika pekerjaan tidak sesuai.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-headset text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-xs">Dukungan 24/7</p>
                            <p class="text-[11px] text-gray-400">Tim CS kami siap membantu kapan saja.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
