@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-bold text-blue-600 mb-4">
                <span class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg font-bold">S</span>
                ServeMix
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to your ServeMix account</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           placeholder="you@example.com"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition pr-11">
                        <button type="button" @click="show = !show" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Create one free</a>
        </p>
    </div>
</div>
@endsection
