@extends('layouts.app')
@section('title', 'Verifikasi Email')
@section('content')
<div class="min-h-screen hero-gradient relative overflow-hidden flex items-center justify-center py-12 px-4">
    {{-- Ambient glow orbs --}}
    <div class="absolute top-[15%] left-[20%] w-72 h-72 bg-indigo-600/30 rounded-full glow-orb animate-float-slow"></div>
    <div class="absolute bottom-[10%] right-[15%] w-80 h-80 bg-violet-600/25 rounded-full glow-orb animate-float-reverse"></div>

    {{-- Floating decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-16 -right-16 w-72 h-72 bg-white/[0.03] rounded-full float-shape-slow"></div>
        <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-white/[0.02] rounded-full float-shape-reverse"></div>
        <div class="absolute top-1/3 right-[8%] w-14 h-14 bg-white/[0.04] rounded-2xl rotate-12 float-shape"></div>
        <div class="absolute bottom-1/4 left-[10%] w-10 h-10 bg-white/[0.03] rounded-full float-shape-reverse"></div>
        <div class="absolute inset-0 shimmer-effect opacity-20"></div>
    </div>

    <div class="relative w-full max-w-md z-10">
        <div class="text-center mb-8 anim-fade-in-down">
            <a href="{{ route('home') }}" class="inline-flex justify-center mb-6 group">
                <img src="{{ asset('images/logo_horizontal.webp') }}" alt="ServeMix" class="h-12 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105">
            </a>
        </div>

        <div class="bg-white/[0.07] backdrop-blur-xl rounded-3xl border border-white/[0.12] p-8 shadow-2xl text-center anim-fade-in-up delay-100">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500/20 to-indigo-500/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-6 border border-blue-400/20 shadow-xl">
                <i class="fas fa-envelope text-blue-400 text-3xl"></i>
            </div>

            <h1 class="text-xl font-extrabold text-white mb-2 tracking-tight">Cek Email Anda</h1>
            <p class="text-blue-200/60 text-sm mb-6 leading-relaxed">
                Kami telah mengirim link verifikasi ke<br>
                <strong class="text-blue-300 font-bold">{{ auth()->user()->email }}</strong>
            </p>

            @if (session('status'))
                <div class="mb-5 p-4 bg-emerald-500/10 border border-emerald-400/20 rounded-2xl text-sm text-emerald-300 flex items-center justify-center gap-2.5 backdrop-blur-sm">
                    <i class="fas fa-check-circle text-emerald-400"></i> {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 p-4 bg-red-500/10 border border-red-400/20 rounded-2xl text-sm text-red-300 flex items-center justify-center gap-2.5 backdrop-blur-sm">
                    <i class="fas fa-exclamation-circle text-red-400"></i> {{ session('error') }}
                </div>
            @endif

            <div class="space-y-4">
                <p class="text-xs text-blue-200/40 font-medium">Belum menerima email? Cek folder spam atau kirim ulang.</p>

                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0 pulse-glow text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 text-blue-300/60 hover:text-white text-sm font-semibold transition-colors duration-300 rounded-2xl hover:bg-white/[0.04]">
                        <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
