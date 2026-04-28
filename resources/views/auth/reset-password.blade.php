@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex justify-center mb-6">
                <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-12 w-auto object-contain">
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
            <p class="text-gray-500 text-sm mt-1">Buat password baru untuk akun kamu</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5" x-data="{ showPass: false, showConfirm: false }">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                           class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <div class="relative">
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition pr-12">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                               placeholder="Ulangi password baru"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition pr-12">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                    <i class="fas fa-lock mr-2"></i>Reset Password
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Kembali ke Login</a>
        </p>
    </div>
</div>
@endsection
