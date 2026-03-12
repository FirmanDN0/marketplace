@extends('layouts.app')
@section('title', 'Setup Profil Provider — Langkah 1')
@section('content')
<div class="max-w-2xl mx-auto">

    @include('provider.onboarding._progress', ['current' => 1])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Langkah 1: Profil Dasar</h2>
            <p class="text-sm text-gray-500 mt-1">Perkenalkan dirimu kepada calon pelanggan</p>
        </div>
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('provider.onboarding.save', 1) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Avatar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <div class="flex items-center gap-4">
                        <div id="avatar-preview" class="w-20 h-20 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <label class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-sm font-medium hover:bg-blue-100 transition">
                                <i class="fas fa-camera"></i> Pilih Foto
                            </span>
                            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </label>
                    </div>
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1.5">Bio / Deskripsi Diri <span class="text-red-500">*</span></label>
                    <textarea id="bio" name="bio" rows="4" required minlength="20" maxlength="1000"
                        placeholder="Ceritakan tentang dirimu, pengalamanmu, dan apa yang bisa kamu tawarkan..."
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('bio', $user->profile->bio) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimal 20 karakter</p>
                    @error('bio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor HP / WhatsApp <span class="text-red-500">*</span></label>
                    <input id="phone" type="tel" name="phone"
                        value="{{ old('phone', $user->profile->phone) }}" required maxlength="20"
                        placeholder="Contoh: 081234567890"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2">
                        Lanjut <i class="fas fa-arrow-right"></i> Keahlian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-preview').innerHTML =
                `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
