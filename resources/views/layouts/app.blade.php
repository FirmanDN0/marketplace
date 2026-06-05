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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<header class="bg-white/75 backdrop-blur-xl border-b border-blue-100/30 sticky top-0 z-50 transition-all duration-300" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0 group transition-transform duration-300 active:scale-95">
                <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-11 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
            </a>

            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <form action="{{ route('services.index') }}" method="GET" class="w-full">
                    <div class="relative group">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-colors group-focus-within:text-blue-600"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan profesional..."
                               class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200/80 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 focus:bg-white transition-all duration-300">
                    </div>
                </form>
            </div>

            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('services.index') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors duration-200">Jelajahi</a>
                @auth
                    <a href="{{ route('messages.index') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors duration-200 relative p-1">
                        <i class="fas fa-comment-dots text-base"></i>
                        <span data-unread-badge="messages" class="absolute -top-1.5 -right-1.5 bg-blue-600 text-white text-[9px] font-bold rounded-full min-w-3.5 h-3.5 px-0.5 flex items-center justify-center border-2 border-white {{ $unreadMessages > 0 ? '' : 'hidden' }}">{{ $unreadMessages }}</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors duration-200 relative p-1">
                        <i class="fas fa-bell text-base"></i>
                        <span data-unread-badge="notifications" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-3.5 h-3.5 px-0.5 flex items-center justify-center border-2 border-white {{ $unreadNotifs > 0 ? '' : 'hidden' }}">{{ $unreadNotifs }}</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors duration-200">Jadi Penyedia Jasa</a>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors duration-200">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-3 rounded-2xl transition duration-300 hover:-translate-y-0.5 active:translate-y-0 shadow-lg shadow-blue-600/15 hover:shadow-xl hover:shadow-blue-600/25">Daftar</a>
                @endauth
            </nav>

            @auth
            <div class="hidden md:block relative ml-4" @click.away="userOpen = false">
                <button @click="userOpen = !userOpen" class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-blue-600/10">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-blue-500/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-left hidden lg:block">
                        <div class="text-sm font-bold text-gray-800 leading-none mb-0.5">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-gray-400 font-medium leading-none">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 mr-1 transition-transform" :class="userOpen ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="userOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                    <a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-th-large w-5 text-center text-gray-400 mr-3"></i>Dasbor</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-user w-5 text-center text-gray-400 mr-3"></i>Profil</a>
                    <a href="{{ route('wallet.index') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-wallet w-5 text-center text-gray-400 mr-3"></i>Dompet</a>
                    <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-heart w-5 text-center text-gray-400 mr-3"></i>Favorit</a>
                    <hr class="my-2 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt w-5 text-center mr-3 text-red-500"></i>Keluar</button>
                    </form>
                </div>
            </div>
            @endauth

            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2.5 rounded-2xl text-gray-600 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition">
                <i class="fas text-lg" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>

        <div x-show="mobileOpen" x-transition class="md:hidden pb-6 border-t border-gray-100 mt-2 pt-4 space-y-2">
            <form action="{{ route('services.index') }}" method="GET" class="mb-4">
                <input type="text" name="q" placeholder="Cari layanan..." class="w-full px-4 py-3 bg-gray-50 rounded-2xl text-sm border border-gray-200 focus:ring-2 focus:ring-blue-600">
            </form>
            <a href="{{ route('services.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Jelajahi</a>
            @auth
                <a href="{{ $dashboardRoute }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Dasbor</a>
                <a href="{{ route('messages.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Pesan</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-xl">Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Masuk</a>
                <a href="{{ route('register') }}" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-blue-600 hover:bg-blue-50">Daftar</a>
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

    <aside class="w-64 shrink-0 bg-[#0b0f19] border-r border-white/[0.04] text-slate-300 flex flex-col fixed inset-y-0 left-0 z-50 transition-transform duration-300"
           :class="sideOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" id="sidebar">
        <div class="p-6 border-b border-white/[0.04]">
            <div class="flex items-center justify-between">
                @if($isAdmin)
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/20"><i class="fas fa-shield-alt text-sm"></i></span>
                        <div class="flex flex-col">
                            <span class="font-bold text-white text-base tracking-tight">ServeMix</span>
                            <span class="text-[9px] font-bold text-violet-400 uppercase tracking-widest leading-none mt-0.5">Admin Console</span>
                        </div>
                    </div>
                @elseif($isProvider)
                    <div class="flex flex-col gap-2">
                        <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-9 w-auto object-contain brightness-0 invert">
                        <span class="text-[9px] font-bold text-blue-400 uppercase tracking-widest bg-blue-500/10 px-2 py-0.5 rounded border border-blue-500/20 inline-block w-max">Provider Panel</span>
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <img src="{{ asset('images/logo_horizontal.png') }}" alt="ServeMix" class="h-9 w-auto object-contain brightness-0 invert">
                        <span class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 inline-block w-max">Client Space</span>
                    </div>
                @endif
                <button type="button" @click="sideOpen = false" class="lg:hidden text-slate-400 hover:text-white w-9 h-9 flex items-center justify-center rounded-xl transition hover:bg-white/[0.04]">
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
                    <h3 class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2.5">{{ $groupName }}</h3>
                    <div class="space-y-1">
                        @foreach($links as $link)
                            @php
                                $isActive = request()->routeIs($link['route'].'*') || request()->routeIs(str_replace('.index','',$link['route']).'.*');
                                $linkClass = $isActive ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/10' : 'text-slate-400 hover:bg-white/[0.03] hover:text-white';
                                $iconClass = $isActive ? 'text-white' : 'text-slate-500 group-hover:text-slate-300';
                            @endphp
                            <a href="{{ isset($link['params']) ? route($link['route'], $link['params']) : route($link['route']) }}" @click="sideOpen = false"
                               class="group flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $linkClass }}">
                                <i class="fas {{ $link['icon'] }} w-5 text-center {{ $iconClass }} transition-colors"></i>{{ $link['label'] }}
                                @if(!empty($link['badge']))
                                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full leading-none">{{ $link['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="p-4 border-t border-white/[0.04] space-y-4">
            <a href="{{ route('home') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-white/[0.03] transition-colors font-medium">
                <span class="flex items-center gap-2"><i class="fas fa-home text-xs text-slate-500"></i> Kembali ke Beranda</span>
            </a>
            
            <div class="flex items-center justify-between gap-3 px-2.5">
                <div class="flex items-center gap-3 min-w-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white/5">
                    @else
                        <div class="w-10 h-10 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold shadow-inner">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-white truncate leading-none mb-1">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500 truncate leading-none">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:text-red-400 hover:bg-white/[0.04] transition" title="Keluar">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 bg-gray-50/50 min-h-screen flex flex-col">
        {{-- Desktop Top Bar (Dashboard) --}}
        <header class="hidden lg:flex items-center justify-end h-20 px-8 bg-white/75 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 transition-all duration-300">
            <div class="flex items-center gap-5">
                {{-- Messages --}}
                <a href="{{ route('messages.index') }}" class="relative p-2.5 text-gray-500 hover:text-blue-600 transition rounded-2xl hover:bg-gray-50 border border-transparent hover:border-gray-100" title="Pesan">
                    <i class="fas fa-comment-dots text-lg"></i>
                    <span data-unread-badge="messages" class="absolute top-1.5 right-1.5 bg-blue-600 text-white text-[9px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center border-2 border-white {{ $unreadMessages > 0 ? '' : 'hidden' }}">{{ $unreadMessages }}</span>
                </a>

                {{-- Notifications Bell --}}
                <a href="{{ route('notifications.index') }}" class="relative p-2.5 text-gray-500 hover:text-blue-600 transition rounded-2xl hover:bg-gray-50 border border-transparent hover:border-gray-100" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span data-unread-badge="notifications" class="absolute top-1.5 right-1.5 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center border-2 border-white {{ $unreadNotifs > 0 ? '' : 'hidden' }}">{{ $unreadNotifs }}</span>
                </a>
                
                <div class="h-6 w-px bg-gray-200/80 mx-2"></div>
                
                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ dashUserOpen: false }" @click.away="dashUserOpen = false">
                    <button @click="dashUserOpen = !dashUserOpen" class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100 focus:outline-none">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-blue-600/10">
                        @else
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-blue-500/20">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-sm font-bold text-gray-800 hidden sm:block">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="dashUserOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="dashUserOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                        <a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-th-large w-5 text-center text-gray-400 mr-3"></i>Dasbor</a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-user w-5 text-center text-gray-400 mr-3"></i>Profil</a>
                        <a href="{{ route('wallet.index') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-wallet w-5 text-center text-gray-400 mr-3"></i>Dompet</a>
                        <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"><i class="fas fa-heart w-5 text-center text-gray-400 mr-3"></i>Favorit</a>
                        <hr class="my-2 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt w-5 text-center mr-3 text-red-500"></i>Keluar</button>
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
    let prevNotifCount = {{ $unreadNotifs }};
    let prevMsgCount = {{ $unreadMessages }};

    function renderBadge(type, count) {
        const value = Number(count) || 0;
        const displayValue = value > 99 ? '99+' : String(value);

        document.querySelectorAll(`[data-unread-badge="${type}"]`).forEach((badge) => {
            badge.textContent = displayValue;
            badge.classList.toggle('hidden', value <= 0);
            
            // Pulse animation when count increases
            if (value > 0) {
                badge.classList.add('animate-pulse');
                setTimeout(() => badge.classList.remove('animate-pulse'), 2000);
            }
        });
    }

    // Notification ping sound using Web Audio API
    function playNotifPing() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.3);
        } catch (e) { /* ignore audio errors */ }
    }

    async function syncUnreadCounts(force = false) {
        const now = Date.now();

        if (syncInFlight) {
            return;
        }

        if (!force && now - lastSyncAt < 1000) {
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
            const newNotifCount = payload.notifications || 0;
            const newMsgCount = payload.messages || 0;
            
            renderBadge('notifications', newNotifCount);
            renderBadge('messages', newMsgCount);

            // Play ping sound when new notifications/messages arrive
            if (newNotifCount > prevNotifCount || newMsgCount > prevMsgCount) {
                playNotifPing();
            }
            
            prevNotifCount = newNotifCount;
            prevMsgCount = newMsgCount;
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

    // Listen to real-time notifications if Echo is available, otherwise fallback to polling
    setTimeout(() => {
        if (window.Echo) {
            window.Echo.private('App.Models.User.' + {{ auth()->id() }})
                .listen('NotificationSent', (e) => {
                    syncUnreadCounts(true);
                });
        } else {
            // Faster polling: 5 seconds instead of 15
            setInterval(() => syncUnreadCounts(false), 5000);
        }
    }, 1000);

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

<!-- Global SweetAlert2 Interceptor for confirm() -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Intercept forms with inline onsubmit
    document.querySelectorAll('form').forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('return confirm')) {
            const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            const msg = match ? match[1] : 'Apakah Anda yakin?';
            
            form.removeAttribute('onsubmit');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-[1.5rem] shadow-2xl font-sans border border-gray-100',
                        confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all mr-3',
                        cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-6 rounded-xl transition-all'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });

    // 2. Intercept buttons or links with inline onclick
    document.querySelectorAll('[onclick*="return confirm"]').forEach(el => {
        const onclickAttr = el.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('return confirm')) {
            const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
            const msg = match ? match[1] : 'Apakah Anda yakin?';
            
            el.removeAttribute('onclick');
            
            el.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-[1.5rem] shadow-2xl font-sans border border-gray-100',
                        confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all mr-3',
                        cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-6 rounded-xl transition-all'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (el.tagName === 'BUTTON' && el.type === 'submit' && el.form) {
                            if (el.name) {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = el.name;
                                hidden.value = el.value || '1';
                                el.form.appendChild(hidden);
                            }
                            el.form.submit();
                        } else if (el.tagName === 'A' && el.href) {
                            window.location.href = el.href;
                        } else if (el.tagName === 'BUTTON' && !el.form) {
                             // Do nothing if it's not a submit button.
                        }
                    }
                });
            });
        }
    });
});
</script>
@stack('scripts')
</body>
</html>
