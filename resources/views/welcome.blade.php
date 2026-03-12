<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ServeMix') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="text-center max-w-md">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <span class="text-white text-2xl font-bold">S</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3">ServeMix</h1>
            <p class="text-gray-500 mb-8">Freelance Marketplace — Find talented providers or offer your services.</p>

            @if (Route::has('login'))
            <nav class="flex items-center justify-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-white border border-gray-200 hover:border-blue-300 text-gray-700 px-6 py-3 rounded-xl font-semibold text-sm transition">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
            @endif
        </div>
    </div>
</body>
</html>
