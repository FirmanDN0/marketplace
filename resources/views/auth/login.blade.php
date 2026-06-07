@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="min-h-screen hero-gradient relative overflow-hidden flex items-center justify-center py-12 px-4">
    {{-- Ambient glow orbs --}}
    <div class="absolute top-[10%] left-[15%] w-72 h-72 bg-indigo-600/30 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[10%] right-[10%] w-96 h-96 bg-violet-600/25 rounded-full glow-orb animate-float-reverse"></div>

    {{-- Floating decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-white/[0.02] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/3 right-[10%] w-16 h-16 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute bottom-1/3 left-[8%] w-12 h-12 bg-white/[0.03] rounded-full float-shape-reverse"></div>
        <div class="absolute inset-0 shimmer-effect opacity-20"></div>
    </div>

    <div class="relative w-full max-w-md z-10">
        <div class="text-center mb-8 anim-fade-in-down">
            <a href="{{ route('home') }}" class="inline-flex justify-center mb-6 group">
                <img src="{{ asset('images/logo_horizontal.webp') }}" alt="ServeMix" class="h-12 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105">
            </a>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Selamat Datang Kembali</h1>
            <p class="text-blue-200/70 text-sm mt-1.5 font-medium">Masuk ke akun ServeMix Anda</p>
        </div>

        <div class="bg-white/[0.07] backdrop-blur-xl rounded-3xl border border-white/[0.12] p-8 shadow-2xl anim-fade-in-up delay-100">
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-400/20 rounded-2xl text-sm text-emerald-300 flex items-center gap-2.5 backdrop-blur-sm">
                    <i class="fas fa-check-circle text-emerald-400"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-400/20 rounded-2xl text-sm text-red-300 flex items-center gap-2.5 backdrop-blur-sm">
                    <i class="fas fa-exclamation-circle text-red-400"></i> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-400/20 rounded-2xl text-sm text-red-300 backdrop-blur-sm">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2.5"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="login" class="block text-sm font-semibold text-blue-100/90 mb-2">Email atau Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" required autocomplete="username" autofocus
                               placeholder="email atau username"
                               class="w-full pl-11 pr-4 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-blue-100/90 mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-11 pr-12 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                        <button type="button" @click="show = !show" tabindex="-1" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/70 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-white/20 bg-white/10 text-blue-500 focus:ring-blue-500/30 focus:ring-offset-0">
                        <span class="text-sm text-blue-200/70 group-hover:text-blue-200/90 transition-colors">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:text-blue-300 font-semibold transition-colors">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 pulse-glow text-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>

            <div class="relative flex items-center justify-center mt-6 mb-6">
                <div class="w-full h-px bg-white/10"></div>
                <span class="absolute bg-white/5 backdrop-blur-md border border-white/10 px-3 py-1 text-xs font-medium text-white/50 rounded-full">atau lanjutkan dengan</span>
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
            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-bold transition-colors">Daftar Gratis</a>
        </p>
    </div>
</div>
@endsection
