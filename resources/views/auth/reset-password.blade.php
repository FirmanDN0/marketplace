@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="min-h-screen hero-gradient relative overflow-hidden flex items-center justify-center py-12 px-4">
    {{-- Ambient glow orbs --}}
    <div class="absolute top-[10%] left-[20%] w-72 h-72 bg-indigo-600/30 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[15%] right-[15%] w-80 h-80 bg-violet-600/25 rounded-full glow-orb animate-float-reverse"></div>

    {{-- Floating decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-28 -right-28 w-[400px] h-[400px] bg-white/[0.02] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/3 right-[10%] w-16 h-16 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute bottom-1/4 left-[10%] w-12 h-12 bg-white/[0.03] rounded-full float-shape-reverse"></div>
        <div class="absolute inset-0 shimmer-effect opacity-20"></div>
    </div>

    <div class="relative w-full max-w-md z-10">
        <div class="text-center mb-8 anim-fade-in-down">
            <a href="{{ route('home') }}" class="inline-flex justify-center mb-6 group">
                <img src="{{ asset('images/logo_horizontal.webp') }}" alt="ServeMix" class="h-12 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105">
            </a>
            <div class="w-16 h-16 bg-white/[0.08] backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-5 border border-white/[0.1] shadow-xl">
                <i class="fas fa-lock text-2xl text-blue-400"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Reset Password</h1>
            <p class="text-blue-200/70 text-sm mt-1.5 font-medium">Buat password baru untuk akun Anda</p>
        </div>

        <div class="bg-white/[0.07] backdrop-blur-xl rounded-3xl border border-white/[0.12] p-8 shadow-2xl anim-fade-in-up delay-100">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-400/20 rounded-2xl text-sm text-red-300 backdrop-blur-sm">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2.5"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5" x-data="{ showPass: false, showConfirm: false }">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-blue-100/90 mb-2">Alamat Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-white/20 text-sm"></i>
                        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                               class="w-full pl-11 pr-4 py-3.5 bg-white/[0.03] border border-white/[0.06] rounded-2xl text-sm text-white/50 cursor-not-allowed backdrop-blur-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-blue-100/90 mb-2">Password Baru</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full pl-11 pr-12 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                        <button type="button" @click="showPass = !showPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/70 transition-colors">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-blue-100/90 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-shield-alt absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm"></i>
                        <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                               placeholder="Ulangi password baru"
                               class="w-full pl-11 pr-12 py-3.5 bg-white/[0.06] border border-white/[0.1] rounded-2xl text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400/30 focus:bg-white/[0.1] transition-all duration-300 backdrop-blur-sm">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/70 transition-colors">
                            <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 pulse-glow text-sm">
                    <i class="fas fa-lock mr-2"></i>Reset Password
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-blue-200/60 mt-8 anim-fade-in delay-300">
            <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-bold transition-colors"><i class="fas fa-arrow-left mr-1.5"></i>Kembali ke Login</a>
        </p>
    </div>
</div>
@endsection
