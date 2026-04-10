@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-bold text-blue-600 mb-4">
                <span class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg font-bold">S</span>
                ServeMix
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Lupa Password?</h1>
            <p class="text-gray-500 text-sm mt-1">Masukkan email kamu, kami akan kirim link reset password</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="you@example.com"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Ingat password? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Kembali ke Login</a>
        </p>
    </div>
</div>
@endsection
