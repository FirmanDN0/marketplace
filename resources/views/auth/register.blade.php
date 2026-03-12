@extends('layouts.app')
@section('title', 'Create Account')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-bold text-blue-600 mb-4">
                <span class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg font-bold">S</span>
                ServeMix
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create an account</h1>
            <p class="text-gray-500 text-sm mt-1">Join ServeMix today — it's free</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-400"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="John Doe"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required maxlength="50" placeholder="johndoe"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required placeholder="Min 8 characters"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition pr-11">
                            <button type="button" @click="show = !show" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="Repeat password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition pr-11">
                            <button type="button" @click="show = !show" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">I want to join as</label>
                    <div class="grid grid-cols-2 gap-3" x-data="{ role: '{{ old('role', 'customer') }}' }">
                        <label @click="role = 'customer'" :class="role === 'customer' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-blue-300'"
                               class="relative flex flex-col items-center gap-2 p-4 rounded-xl border cursor-pointer transition">
                            <input type="radio" name="role" value="customer" x-model="role" class="sr-only">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-shopping-bag"></i></div>
                            <div class="text-sm font-semibold text-gray-800">Customer</div>
                            <div class="text-xs text-gray-500">Buy services</div>
                        </label>
                        <label @click="role = 'provider'" :class="role === 'provider' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-blue-300'"
                               class="relative flex flex-col items-center gap-2 p-4 rounded-xl border cursor-pointer transition">
                            <input type="radio" name="role" value="provider" x-model="role" class="sr-only">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-briefcase"></i></div>
                            <div class="text-sm font-semibold text-gray-800">Provider</div>
                            <div class="text-xs text-gray-500">Sell services</div>
                        </label>
                    </div>
                </div>

                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                @error('g-recaptcha-response')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg shadow-blue-200/50">
                    Create Account
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>
        </p>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
