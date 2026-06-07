@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/20">
            <i class="fas fa-user-edit text-sm"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Edit: {{ $user->name }}</h1>
    </div>

    @if($user->role === 'customer' && $user->provider_setup_step >= 3)
        <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <h3 class="font-bold text-amber-800 text-sm">Aplikasi Provider Menunggu Persetujuan</h3>
                    <p class="text-xs text-amber-700 mt-0.5">Pengguna ini telah melengkapi profil dan siap menjadi Provider.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.users.approve_provider', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengguna ini menjadi Provider?')">
                @csrf
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white rounded-xl font-bold text-sm shadow-sm transition-all whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Setujui
                </button>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100/80"><h3 class="font-extrabold text-gray-900">Detail Pengguna</h3></div>
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span></label>
                    <input id="password" type="password" name="password" placeholder="Leave blank to keep current"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                        <select id="role" name="role" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="provider" {{ $user->role === 'provider' ? 'selected' : '' }}>Provider</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="status" name="status" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="banned" {{ $user->status === 'banned' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-6 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-500/15">Perbarui</button>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-bold transition">Batal</a>
                </div>
            </form>
        </div>
    </div>


    @if($user->profile && ($user->role === 'provider' || $user->provider_setup_step >= 1))
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100/80"><h3 class="font-extrabold text-gray-900">Data Profil Provider</h3></div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Bio</label>
                    <p class="text-sm text-gray-800">{{ $user->profile->bio ?? '-' }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Keahlian (Skills)</label>
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @if($user->profile->skills)
                                @foreach($user->profile->skills as $skill)
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">{{ $skill }}</span>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Bahasa</label>
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @if($user->profile->languages)
                                @foreach($user->profile->languages as $lang)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">{{ $lang }}</span>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pengalaman</label>
                        <p class="text-sm text-gray-800">{{ $user->profile->experience_years ? $user->profile->experience_years . ' Tahun' : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tarif per Jam</label>
                        <p class="text-sm text-gray-800">{{ $user->profile->hourly_rate ? 'Rp ' . number_format($user->profile->hourly_rate, 0, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Lokasi</label>
                        <p class="text-sm text-gray-800">{{ $user->profile->city ?? '-' }}, {{ $user->profile->country ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Website / Portfolio</label>
                        <p class="text-sm text-gray-800">
                            @if($user->profile->website)
                                <a href="{{ $user->profile->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $user->profile->website }}</a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor HP</label>
                        <p class="text-sm text-gray-800">{{ $user->profile->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
