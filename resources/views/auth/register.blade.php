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
                <img src="{{ asset('images/logo_horizontal.webp') }}" alt="ServeMix" class="h-12 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105">
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

                <input type="hidden" name="role" value="customer">

                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-theme="dark"></div>
                @error('g-recaptcha-response')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 pulse-glow text-sm">
                    <i class="fas fa-user-plus mr-2"></i>Buat Akun
                </button>
            </form>

            <div class="relative flex items-center justify-center mt-6 mb-6">
                <div class="w-full h-px bg-white/10"></div>
                <span class="absolute bg-white/5 backdrop-blur-md border border-white/10 px-3 py-1 text-xs font-medium text-white/50 rounded-full">atau daftar dengan</span>
            </div>

            <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center gap-3 py-3.5 bg-white/[0.06] hover:bg-white/[0.1] border border-white/[0.1] text-white font-bold rounded-2xl transition-all duration-300 backdrop-blur-sm text-sm hover:shadow-lg hover:shadow-white/[0.05] hover:-translate-y-0.5">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Google
            </a>
        </div>

        <p class="text-center text-sm text-blue-200/60 mt-8 anim-fade-in delay-300">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
