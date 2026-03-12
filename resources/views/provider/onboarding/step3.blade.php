@extends('layouts.app')
@section('title', 'Setup Profil Provider — Langkah 3')
@section('content')
<div class="max-w-2xl mx-auto">

    @include('provider.onboarding._progress', ['current' => 3])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Langkah 3: Lokasi & Tarif</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lokasi dan harga layananmu</p>
        </div>
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('provider.onboarding.save', 3) }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1.5">Negara <span class="text-red-500">*</span></label>
                        <input id="country" type="text" name="country"
                            value="{{ old('country', $user->profile->country) }}" required maxlength="100"
                            placeholder="Contoh: Indonesia"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('country')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">Kota <span class="text-red-500">*</span></label>
                        <input id="city" type="text" name="city"
                            value="{{ old('city', $user->profile->city) }}" required maxlength="100"
                            placeholder="Contoh: Jakarta"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('city')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1.5">Website / Portfolio <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input id="website" type="url" name="website"
                        value="{{ old('website', $user->profile->website) }}" maxlength="255"
                        placeholder="https://portofoliomu.com"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('website')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-1.5">Tarif per Jam <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input id="hourly_rate" type="number" name="hourly_rate"
                            value="{{ old('hourly_rate', $user->profile->hourly_rate) }}" min="0" max="9999999"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Estimasi tarif per jam untuk konsultasi atau negosiasi harga</p>
                    @error('hourly_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('provider.onboarding.show', 2) }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition flex items-center gap-1.5">
                        <i class="fas fa-arrow-left text-xs"></i> Kembali
                    </a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                        Selesai & Mulai Berjualan <i class="fas fa-trophy"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
