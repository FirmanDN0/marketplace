<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Checkout') - ServeMix</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    {{-- Checkout Top Bar --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Left: Back + Logo --}}
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition" title="Kembali">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                        <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-8 w-auto object-contain">
                    </a>
                </div>

                {{-- Center: Checkout Steps --}}
                <div class="hidden sm:flex items-center gap-3">
                    @php
                        $checkoutStep = $checkoutStep ?? 1;
                    @endphp
                    @foreach([
                        ['num' => 1, 'label' => 'Konfirmasi'],
                        ['num' => 2, 'label' => 'Pembayaran'],
                        ['num' => 3, 'label' => 'Selesai'],
                    ] as $step)
                        @php
                            $isActive = $checkoutStep >= $step['num'];
                            $isCurrent = $checkoutStep === $step['num'];
                        @endphp
                        @if($step['num'] > 1)
                            <div class="w-8 h-px {{ $isActive ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                        @endif
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center transition-all
                                {{ $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-100' : ($isActive ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400') }}">
                                @if($isActive && !$isCurrent)
                                    <i class="fas fa-check text-[10px]"></i>
                                @else
                                    {{ $step['num'] }}
                                @endif
                            </div>
                            <span class="text-xs font-semibold {{ $isCurrent ? 'text-blue-600' : ($isActive ? 'text-gray-700' : 'text-gray-400') }}">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Right: Security badge --}}
                <div class="flex items-center gap-2 text-gray-400">
                    <i class="fas fa-shield-alt text-green-500 text-sm"></i>
                    <span class="text-xs font-medium text-gray-500 hidden sm:inline">Transaksi Aman</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    <div class="fixed top-4 right-4 z-[100] space-y-2" x-data="{ msgs: {{ json_encode(array_values(array_filter([
        session('success') ? ['type'=>'success','text'=>session('success')] : null,
        session('info') ? ['type'=>'info','text'=>session('info')] : null,
        (isset($errors) && $errors->any()) ? ['type'=>'error','text'=>$errors->first()] : null
    ]))) }} }" x-init="setTimeout(() => msgs = [], 5000)">
        <template x-for="(msg, i) in msgs" :key="i">
            <div x-show="true" x-transition
                 :class="{
                    'bg-green-50 border-green-400 text-green-800': msg.type === 'success',
                    'bg-blue-50 border-blue-400 text-blue-800': msg.type === 'info',
                    'bg-red-50 border-red-400 text-red-800': msg.type === 'error'
                 }"
                 class="px-4 py-3 rounded-xl border shadow-lg text-sm flex items-center gap-3 min-w-[250px] sm:min-w-[300px] max-w-[90vw]">
                <i class="fas" :class="{'fa-check-circle': msg.type==='success', 'fa-info-circle': msg.type==='info', 'fa-exclamation-circle': msg.type==='error'}"></i>
                <span x-text="msg.text"></span>
                <button @click="msgs.splice(i, 1)" class="ml-auto text-current opacity-50 hover:opacity-100"><i class="fas fa-times"></i></button>
            </div>
        </template>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    {{-- Minimal Footer --}}
    <footer class="bg-white border-t border-gray-100 py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} ServeMix. Semua hak dilindungi.</p>
            <div class="flex items-center gap-3 text-xs text-gray-400">
                <i class="fas fa-lock text-green-500"></i>
                <span>Pembayaran Terenkripsi SSL</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
