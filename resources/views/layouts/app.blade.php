<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ServeMix') - Marketplace</title>
    
    {{-- SEO & Meta Tags --}}
    <meta name="description" content="@yield('meta_description', 'ServeMix is the ultimate freelance marketplace to find professional services for your business.')">
    <meta property="og:title" content="@yield('meta_title', 'ServeMix - Find Professional Services')">
    <meta property="og:description" content="@yield('meta_description', 'ServeMix is the ultimate freelance marketplace to find professional services for your business.')">
    <meta property="og:type" content="@yield('meta_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('meta_image', asset('images/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=5">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v=5">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-800 antialiased">

@auth
@php
    $isDashboard = request()->routeIs('admin.*', 'provider.*', 'customer.*', 'profile.*', 'wallet.*', 'topup.*', 'customer-service.*', 'favorites.*');
    $isAdmin = auth()->user()->isAdmin();
    $isProvider = auth()->user()->isProvider();
    $dashboardRoute = $isAdmin ? route('admin.dashboard')
        : ($isProvider ? route('provider.dashboard')
        : route('customer.dashboard'));
    $currentUserId = auth()->id();
    $unreadNotifs = \App\Models\AppNotification::where('user_id', $currentUserId)->whereNull('read_at')->count();
    $unreadMessages = \App\Models\Message::whereNull('read_at')
        ->where('sender_id', '!=', $currentUserId)
        ->whereHas('conversation', fn($query) => $query->where('customer_id', $currentUserId)->orWhere('provider_id', $currentUserId))
        ->count();
@endphp
@endauth

{{-- NAVBAR (public/guest pages only) --}}
@if(!($isDashboard ?? false) || !auth()->check())
<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-10 w-auto object-contain">
            </a>

            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <form action="{{ route('services.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan..."
                               class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-full text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:bg-white transition-all duration-300">
                    </div>
                </form>
            </div>

            <nav class="hidden md:flex items-center gap-4">
                <a href="{{ route('services.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Jelajahi</a>
                @auth
                    <a href="{{ route('messages.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition relative">
                        Pesan
                        <span data-unread-badge="messages" class="absolute -top-2 -right-4 bg-blue-600 text-white text-[10px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center {{ $unreadMessages > 0 ? '' : 'hidden' }}">{{ $unreadMessages }}</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition relative">
                        <i class="fas fa-bell"></i>
                        <span data-unread-badge="notifications" class="absolute -top-2 -right-3 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center {{ $unreadNotifs > 0 ? '' : 'hidden' }}">{{ $unreadNotifs }}</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Jadi Penyedia Jasa</a>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-full transition shadow-md shadow-blue-200">Daftar</a>
                @endauth
            </nav>

            @auth
            <div class="hidden md:block relative ml-2" @click.away="userOpen = false">
                <button @click="userOpen = !userOpen" class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition">
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                </button>
                <div x-show="userOpen" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                    <a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-th-large w-5 text-center text-gray-400 mr-2"></i>Dasbor</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-user w-5 text-center text-gray-400 mr-2"></i>Profil</a>
                    <a href="{{ route('wallet.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-wallet w-5 text-center text-gray-400 mr-2"></i>Dompet</a>
                    <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-heart w-5 text-center text-gray-400 mr-2"></i>Favorit</a>
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt w-5 text-center mr-2"></i>Keluar</button>
                    </form>
                </div>
            </div>
            @endauth

            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>

        <div x-show="mobileOpen" x-transition class="md:hidden pb-4 border-t border-gray-100 mt-2 pt-4 space-y-2">
            <form action="{{ route('services.index') }}" method="GET" class="mb-3">
                <input type="text" name="q" placeholder="Cari layanan..." class="w-full px-4 py-2 bg-gray-100 rounded-lg text-sm border-0 focus:ring-2 focus:ring-blue-500">
            </form>
            <a href="{{ route('services.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Jelajahi</a>
            @auth
                <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Dasbor</a>
                <a href="{{ route('messages.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Pesan</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Masuk</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-600 hover:bg-blue-50">Daftar</a>
            @endauth
        </div>
    </div>
</header>
@endif

{{-- FLASH MESSAGES --}}
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

@auth
@if($isDashboard)
{{-- DASHBOARD LAYOUT --}}
<div class="flex min-h-screen" x-data="{ sideOpen: false }">
    {{-- Mobile Overlay --}}
    <div x-show="sideOpen" x-transition.opacity @click="sideOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- Mobile Top Bar --}}
    <div class="fixed top-0 left-0 right-0 z-30 lg:hidden {{ $isAdmin ? 'bg-slate-900' : 'bg-white border-b border-gray-200' }} h-14 flex items-center px-4 gap-3">
        <button @click="sideOpen = !sideOpen" class="{{ $isAdmin ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-blue-600' }} p-2 -ml-2 rounded-lg transition">
            <i class="fas fa-bars text-lg"></i>
        </button>
        @if($isAdmin)
            <span class="font-bold text-white text-sm">Panel Admin</span>
        @elseif($isProvider)
            <span class="font-bold text-gray-800 text-sm">Dasbor Penyedia</span>
        @else
            <span class="font-bold text-gray-800 text-sm">Akun Saya</span>
        @endif
        <a href="{{ route('home') }}" class="ml-auto text-xs {{ $isAdmin ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-blue-600' }} transition">
            <i class="fas fa-home"></i>
        </a>
    </div>

    <aside class="w-64 shrink-0 {{ $isAdmin ? 'bg-slate-900 text-gray-300' : 'bg-white border-r border-gray-200 text-gray-700' }} flex flex-col fixed inset-y-0 left-0 z-50 transition-transform duration-300"
           :class="sideOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" id="sidebar">
        <div class="p-5 {{ $isAdmin ? 'border-b border-slate-700' : 'border-b border-gray-100' }}">
            <div class="flex items-start justify-between">
                @if($isAdmin)
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center shrink-0"><i class="fas fa-shield-alt text-sm"></i></span>
                        <span class="font-bold text-white text-lg">Panel Admin</span>
                    </div>
                @elseif($isProvider)
                    <div class="flex flex-col gap-2">
                        <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-8 w-auto object-contain">
                        <span class="text-[10px] font-bold text-blue-700 uppercase tracking-widest bg-blue-100/50 px-2 py-1 rounded-md inline-block w-max border border-blue-200">Dasbor Penyedia</span>
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-8 w-auto object-contain">
                        <span class="text-[10px] font-bold text-blue-700 uppercase tracking-widest bg-blue-100/50 px-2 py-1 rounded-md inline-block w-max border border-blue-200">Akun Saya</span>
                    </div>
                @endif
                <button type="button" @click="sideOpen = false" class="lg:hidden {{ $isAdmin ? 'text-gray-400 hover:text-white' : 'text-gray-400 hover:text-gray-600' }} w-8 h-8 flex items-center justify-center rounded-lg transition hover:bg-black/10">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto no-scrollbar">
            @php
                $navGroups = [];
                if($isAdmin) {
                    $navGroups = [
                        'Ringkasan' => [
                            ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dasbor'],
                        ],
                        'Marketplace' => [
                            ['route' => 'admin.services.index', 'icon' => 'fa-briefcase', 'label' => 'Layanan', 'badge' => $adminPendingCounts['services'] ?? 0],
                            ['route' => 'admin.categories.index', 'icon' => 'fa-tags', 'label' => 'Kategori'],
                            ['route' => 'admin.orders.index', 'icon' => 'fa-box', 'label' => 'Pesanan'],
                            ['route' => 'admin.reviews.index', 'icon' => 'fa-star', 'label' => 'Ulasan'],
                        ],
                        'Keuangan & Laporan' => [
                            ['route' => 'admin.withdrawals.index', 'icon' => 'fa-money-bill-wave', 'label' => 'Penarikan', 'badge' => $adminPendingCounts['withdrawals'] ?? 0],
                            ['route' => 'admin.reports', 'icon' => 'fa-chart-bar', 'label' => 'Laporan'],
                        ],
                        'Manajemen Pengguna' => [
                            ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Pengguna'],
                            ['route' => 'admin.disputes.index', 'icon' => 'fa-exclamation-triangle', 'label' => 'Sengketa', 'badge' => $adminPendingCounts['disputes'] ?? 0],
                            ['route' => 'admin.customer-service.index', 'icon' => 'fa-headset', 'label' => 'Dukungan', 'badge' => $adminPendingCounts['cs'] ?? 0],
                        ],
                        'Akun' => [
                            ['route' => 'profile.edit', 'icon' => 'fa-cog', 'label' => 'Pengaturan'],
                            ['route' => 'customer-service.index', 'icon' => 'fa-headset', 'label' => 'Bantuan'],
                        ],
                    ];
                } elseif($isProvider) {
                    $navGroups = [
                        'Ringkasan' => [
                            ['route' => 'provider.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dasbor'],
                        ],
                        'Bisnis Saya' => [
                            ['route' => 'provider.orders.index', 'icon' => 'fa-box', 'label' => 'Pesanan'],
                            ['route' => 'provider.services.index', 'icon' => 'fa-briefcase', 'label' => 'Layanan Saya'],
                        ],
                        'Keuangan' => [
                            ['route' => 'wallet.index', 'icon' => 'fa-wallet', 'label' => 'Dompet'],
                            ['route' => 'provider.withdraw.index', 'icon' => 'fa-money-bill-wave', 'label' => 'Penarikan'],
                        ],
                        'Akun' => [
                            ['route' => 'provider.profile', 'params' => ['username' => auth()->user()->username], 'icon' => 'fa-user', 'label' => 'Lihat Profil'],
                            ['route' => 'profile.edit', 'icon' => 'fa-cog', 'label' => 'Pengaturan'],
                            ['route' => 'customer-service.index', 'icon' => 'fa-headset', 'label' => 'Bantuan'],
                        ]
                    ];
                } else {
                    $navGroups = [
                        'Ringkasan' => [
                            ['route' => 'customer.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dasbor'],
                        ],
                        'Aktivitas' => [
                            ['route' => 'customer.orders.index', 'icon' => 'fa-box', 'label' => 'Pesanan Saya'],
                            ['route' => 'favorites.index', 'icon' => 'fa-heart', 'label' => 'Favorit'],
                        ],
                        'Keuangan' => [
                            ['route' => 'wallet.index', 'icon' => 'fa-wallet', 'label' => 'Dompet'],
                        ],
                        'Akun' => [
                            ['route' => 'profile.edit', 'icon' => 'fa-cog', 'label' => 'Pengaturan'],
                            ['route' => 'customer-service.index', 'icon' => 'fa-headset', 'label' => 'Bantuan'],
                        ]
                    ];
                }
            @endphp

            @foreach($navGroups as $groupName => $links)
                <div class="mb-6">
                    <h3 class="px-3 text-[11px] font-bold {{ $isAdmin ? 'text-slate-500' : 'text-gray-400' }} uppercase tracking-wider mb-2">{{ $groupName }}</h3>
                    <div class="space-y-1">
                        @foreach($links as $link)
                            @php
                                $isActive = request()->routeIs($link['route'].'*') || request()->routeIs(str_replace('.index','',$link['route']).'.*');
                                if($isAdmin) {
                                    $linkClass = $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white';
                                    $iconClass = $isActive ? 'text-white' : 'text-slate-500 group-hover:text-slate-300';
                                } else {
                                    $linkClass = $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600';
                                    $iconClass = $isActive ? 'text-white' : 'text-gray-400 group-hover:text-blue-600';
                                }
                            @endphp
                            <a href="{{ isset($link['params']) ? route($link['route'], $link['params']) : route($link['route']) }}" @click="sideOpen = false"
                               class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $linkClass }}">
                                <i class="fas {{ $link['icon'] }} w-5 text-center {{ $iconClass }} transition"></i>{{ $link['label'] }}
                                @if(!empty($link['badge']))
                                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $link['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="p-4 {{ $isAdmin ? 'border-t border-slate-800' : 'border-t border-gray-100' }} space-y-3">
            <a href="{{ route('home') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm {{ $isAdmin ? 'text-slate-400 hover:text-white hover:bg-slate-800' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }} transition">
                <span class="flex items-center gap-2"><i class="fas fa-home text-xs"></i> Kembali ke Beranda</span>
            </a>
            
            <div class="flex items-center justify-between gap-3 px-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-full {{ $isAdmin ? 'bg-slate-700 text-gray-300' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-sm font-medium truncate {{ $isAdmin ? 'text-white' : 'text-gray-800' }}">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] truncate {{ $isAdmin ? 'text-slate-500' : 'text-gray-400' }}">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center transition {{ $isAdmin ? 'text-slate-400 hover:text-red-400 hover:bg-slate-800' : 'text-gray-400 hover:text-red-500 hover:bg-red-50' }}" title="Keluar">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 bg-gray-50 min-h-screen flex flex-col">
        {{-- Desktop Top Bar (Dashboard) --}}
        <header class="hidden lg:flex items-center justify-end h-16 px-8 bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40">
            <div class="flex items-center gap-4">
                {{-- Notifications Bell --}}
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:text-blue-600 transition rounded-full hover:bg-gray-100">
                    <i class="fas fa-bell text-lg"></i>
                    <span data-unread-badge="notifications" class="absolute top-1.5 right-1.5 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center border-2 border-white {{ $unreadNotifs > 0 ? '' : 'hidden' }}">{{ $unreadNotifs }}</span>
                </a>
                
                <div class="h-6 w-px bg-gray-200 mx-2"></div>
                
                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ dashUserOpen: false }" @click.away="dashUserOpen = false">
                    <button @click="dashUserOpen = !dashUserOpen" class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition focus:outline-none">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    
                    <div x-show="dashUserOpen" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-th-large w-5 text-center text-gray-400 mr-2"></i>Dasbor</a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-user w-5 text-center text-gray-400 mr-2"></i>Profil</a>
                        <a href="{{ route('wallet.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-wallet w-5 text-center text-gray-400 mr-2"></i>Dompet</a>
                        <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-heart w-5 text-center text-gray-400 mr-2"></i>Favorit</a>
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt w-5 text-center mr-2"></i>Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-4 pt-[4.5rem] sm:p-6 sm:pt-[5rem] lg:p-8 lg:pt-6">
            @yield('content')
        </div>
    </main>
</div>
@else
<main>
    @yield('content')
</main>
@endif
@else
<main>
    @yield('content')
</main>
@endauth

@auth
<script>
(() => {
    const syncUrl = '{{ route('notifications.sync-counts') }}';
    let syncInFlight = false;
    let lastSyncAt = 0;

    function renderBadge(type, count) {
        const value = Number(count) || 0;
        const displayValue = value > 99 ? '99+' : String(value);

        document.querySelectorAll(`[data-unread-badge="${type}"]`).forEach((badge) => {
            badge.textContent = displayValue;
            badge.classList.toggle('hidden', value <= 0);
        });
    }

    async function syncUnreadCounts(force = false) {
        const now = Date.now();

        if (syncInFlight) {
            return;
        }

        if (!force && now - lastSyncAt < 1500) {
            return;
        }

        syncInFlight = true;

        try {
            const response = await fetch(syncUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            renderBadge('notifications', payload.notifications || 0);
            renderBadge('messages', payload.messages || 0);
            lastSyncAt = Date.now();
        } catch (error) {
            // Ignore transient sync errors.
        } finally {
            syncInFlight = false;
        }
    }

    window.syncUiUnreadCounts = syncUnreadCounts;

    window.addEventListener('focus', () => syncUnreadCounts(true));
    window.addEventListener('pageshow', () => syncUnreadCounts(true));
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            syncUnreadCounts(true);
        }
    });
    document.addEventListener('ui:sync-unread-counts', () => syncUnreadCounts(true));

    setInterval(() => syncUnreadCounts(false), 15000);
    syncUnreadCounts(true);
})();

function toggleFavorite(serviceId, btn) {
    fetch('/favorites/' + serviceId + '/toggle', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        const icon = btn.querySelector('i');
        const label = btn.querySelector('span');
        if (data.status === 'added') {
            icon.classList.replace('far', 'fas');
            btn.classList.add('text-red-500');
            btn.classList.remove('text-gray-400');
            if (label) label.textContent = 'Saved';
        } else {
            icon.classList.replace('fas', 'far');
            btn.classList.remove('text-red-500');
            btn.classList.add('text-gray-400');
            if (label) label.textContent = 'Save';
        }
    });
}
</script>
@endauth

@stack('scripts')
</body>
</html>
