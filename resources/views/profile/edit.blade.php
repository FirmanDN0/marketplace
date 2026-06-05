@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-8 flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
            <i class="fas fa-user-circle text-lg"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Profil Saya</h1>
            <p class="text-gray-500 text-sm font-medium mt-0.5">Kelola informasi pribadi dan pengaturan akun</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Avatar Section --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/80">
                    <h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-camera text-blue-500 text-sm"></i>Foto Profil</h3>
                </div>
                <div class="p-6 text-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-28 h-28 rounded-full object-cover mx-auto mb-4 ring-4 ring-blue-50">
                    @else
                        <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-4xl font-bold mx-auto mb-4 shadow-lg shadow-blue-500/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-400 mt-2">JPG, PNG or WebP. Max 2 MB.</p>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-4 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15">Unggah Foto</button>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-100/80">
                    <h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-lock text-amber-500 text-sm"></i>Ubah Kata Sandi</h3>
                </div>
                <div class="p-6">
                    @if($errors->has('current_password') || $errors->has('password'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label for="current_password" class="block text-sm font-bold text-gray-700 mb-1.5">Kata Sandi Saat Ini</label>
                            <input id="current_password" type="password" name="current_password" required
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-bold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                            <input id="new_password" type="password" name="password" required placeholder="Min 8 characters"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2.5 rounded-2xl font-bold text-sm transition">Perbarui Kata Sandi</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Profile Info Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/80">
                    <h3 class="font-extrabold text-gray-900 flex items-center gap-2"><i class="fas fa-id-card text-indigo-500 text-sm"></i>Informasi Pribadi</h3>
                </div>
                <div class="p-6">
                    @if($errors->has('name') || $errors->has('username') || $errors->has('bio'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="100"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required maxlength="50"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1.5">Bio</label>
                            <textarea id="bio" name="bio" rows="3" placeholder="Tell us about yourself…"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('bio', $profile->bio) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="+1 555 000 0000"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-1.5">Website</label>
                                <input id="website" type="url" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://example.com"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                                <input id="country" type="text" name="country" value="{{ old('country', $profile->country) }}"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                                <input id="city" type="text" name="city" value="{{ old('city', $profile->city) }}"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="skills" class="block text-sm font-medium text-gray-700 mb-1.5">Skills <span class="text-gray-400 font-normal">(comma separated)</span></label>
                            <input id="skills" type="text" name="skills" value="{{ old('skills', is_array($profile->skills) ? implode(', ', $profile->skills) : $profile->skills) }}" placeholder="e.g. PHP, Design, Marketing"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1.5">Experience (years)</label>
                                <input id="experience_years" type="number" name="experience_years" value="{{ old('experience_years', $profile->experience_years) }}" min="0" max="50"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            @if($user->isProvider())
                            <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-1.5">Hourly Rate ($)</label>
                                <input id="hourly_rate" type="number" name="hourly_rate" value="{{ old('hourly_rate', $profile->hourly_rate) }}" step="0.01" min="0" placeholder="0.00"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            @endif
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15 hover:-translate-y-0.5 active:translate-y-0 duration-300">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Danger Zone --}}
    <div class="mt-8 bg-white rounded-3xl shadow-sm border border-red-200/80 overflow-hidden" x-data="{ showDelete: false }">
        <div class="px-5 py-4 border-b border-red-100 bg-red-50">
            <h3 class="font-semibold text-red-700"><i class="fas fa-exclamation-triangle mr-1.5"></i> Zona Berbahaya</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Setelah akun dihapus, semua data akan hilang secara permanen dan tidak bisa dikembalikan.</p>
            <button @click="showDelete = !showDelete" class="bg-red-50 hover:bg-red-100 text-red-600 px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                <i class="fas fa-trash-alt mr-1.5"></i> Hapus Akun Saya
            </button>
            <div x-show="showDelete" x-transition class="mt-4 border-t border-red-100 pt-4">
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('YAKIN HAPUS AKUN? Semua data akan hilang permanen!')">
                    @csrf @method('DELETE')
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Masukkan password untuk konfirmasi</label>
                        <input type="password" name="password" required placeholder="Password Anda"
                               class="w-full sm:w-80 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('delete_password')<span class="text-red-500 text-xs block mt-1">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                        Hapus Akun Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

