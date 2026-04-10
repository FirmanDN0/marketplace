@extends('layouts.app')
@section('title', 'Verifikasi Email')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-bold text-blue-600 mb-4">
                <span class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg font-bold">S</span>
                ServeMix
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-envelope text-blue-600 text-3xl"></i>
            </div>

            <h1 class="text-xl font-bold text-gray-900 mb-2">Cek Email Kamu</h1>
            <p class="text-gray-500 text-sm mb-6">
                Kami telah mengirim link verifikasi ke<br>
                <strong class="text-gray-700">{{ auth()->user()->email }}</strong>
            </p>

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i> {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-400"></i> {{ session('error') }}
                </div>
            @endif

            <div class="space-y-3">
                <p class="text-xs text-gray-400">Belum menerima email? Cek folder spam atau kirim ulang.</p>

                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 text-gray-500 hover:text-gray-700 text-sm font-medium transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
