@extends('layouts.app')
@section('title', 'Daftar Akun')
@section('content')
<div class="min-h-screen hero-gradient relative overflow-hidden flex items-center justify-center py-12 px-4">
    {{-- Ambient glow orbs --}}
    <div class="absolute top-[5%] right-[20%] w-80 h-80 bg-indigo-600/30 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[15%] left-[10%] w-72 h-72 bg-violet-600/25 rounded-full glow-orb animate-float-reverse"></div>
    <div class="absolute top-[60%] right-[5%] w-48 h-48 bg-blue-500/20 rounded-full glow-orb float-shape"></div>

    {{-- Floating decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-20 -right-20 w-[450px] h-[450px] bg-white/[0.02] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/4 left-[8%] w-14 h-14 bg-white/[0.04] rounded-2xl rotate-45 float-shape"></div>
        <div class="absolute bottom-1/4 right-[12%] w-10 h-10 bg-white/[0.03] rounded-full float-shape-reverse"></div>
        <div class="absolute inset-0 shimmer-effect opacity-20"></div>
    </div>

    <div class="relative w-full max-w-lg z-10">
        <div class="text-center mb-8 anim-fade-in-down">
            <a href="{{ route('home') }}" class="inline-flex justify-center mb-6 group">
                <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-12 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105">
            </a>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Buat Akun Baru</h1>
            <p class="text-blue-200/70 text-sm mt-1.5 font-medium">Bergabunglah dengan ServeMix — gratis selamanya</p>
        </div>

        <div class="bg-white/[0.07] backdrop-blur-xl rounded-3xl border border-white/[0.12] p-8 shadow-2xl anim-fade-in-up delay-100">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-400/20 rounded-2xl text-sm text-red-300 space-y-1 backdrop-blur-sm">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2.5"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-blue-100/90 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="John Doe"
                                   class="w-full pl-11 pr-4 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                        </div>
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-semibold text-blue-100/90 mb-2">Username</label>
                        <div class="relative">
                            <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required maxlength="50" placeholder="johndoe"
                                   class="w-full pl-11 pr-4 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-blue-100/90 mb-2">Alamat Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="anda@contoh.com"
                               class="w-full pl-11 pr-4 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-semibold text-blue-100/90 mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required placeholder="Min 8 karakter"
                                   class="w-full pl-11 pr-12 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                            <button type="button" @click="show = !show" tabindex="-1" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/70 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-sm font-semibold text-blue-100/90 mb-2">Konfirmasi</label>
                        <div class="relative">
                            <i class="fas fa-shield-alt absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                            <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="Ulangi password"
                                   class="w-full pl-11 pr-12 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                            <button type="button" @click="show = !show" tabindex="-1" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/70 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-blue-100/90 mb-3">Saya ingin bergabung sebagai</label>
                    <div class="grid grid-cols-2 gap-3" x-data="{ role: '{{ old('role', 'customer') }}' }">
                        <label @click="role = 'customer'" :class="role === 'customer' ? 'border-blue-400/50 bg-blue-500/15 ring-2 ring-blue-400/20' : 'border-white/[0.1] hover:border-blue-400/30 bg-white/[0.04]'"
                               class="relative flex flex-col items-center gap-2.5 p-5 rounded-2xl border cursor-pointer transition-all duration-300 backdrop-blur-sm group">
                            <input type="radio" name="role" value="customer" x-model="role" class="sr-only">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20 transition-transform duration-300 group-hover:scale-105">
                                <i class="fas fa-shopping-bag text-lg"></i>
                            </div>
                            <div class="text-sm font-bold text-white">Customer</div>
                            <div class="text-xs text-blue-200/50 font-medium">Beli layanan</div>
                        </label>
                        <label @click="role = 'provider'" :class="role === 'provider' ? 'border-emerald-400/50 bg-emerald-500/15 ring-2 ring-emerald-400/20' : 'border-white/[0.1] hover:border-emerald-400/30 bg-white/[0.04]'"
                               class="relative flex flex-col items-center gap-2.5 p-5 rounded-2xl border cursor-pointer transition-all duration-300 backdrop-blur-sm group">
                            <input type="radio" name="role" value="provider" x-model="role" class="sr-only">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20 transition-transform duration-300 group-hover:scale-105">
                                <i class="fas fa-briefcase text-lg"></i>
                            </div>
                            <div class="text-sm font-bold text-white">Provider</div>
                            <div class="text-xs text-blue-200/50 font-medium">Jual layanan</div>
                        </label>
                    </div>
                </div>

                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-theme="dark"></div>
                @error('g-recaptcha-response')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 pulse-glow text-sm">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-blue-200/60 mt-8 anim-fade-in delay-300">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
