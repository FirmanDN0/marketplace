<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ServeMix') - Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-800 antialiased">

@auth
@php
    $isDashboard = request()->routeIs('admin.*', 'provider.*', 'customer.*', 'profile.*', 'wallet.*', 'topup.*', 'customer-service.*');
    $isAdmin = auth()->user()->isAdmin();
    $isProvider = auth()->user()->isProvider();
    $dashboardRoute = $isAdmin ? route('admin.dashboard')
        : ($isProvider ? route('provider.dashboard')
        : route('customer.dashboard'));
    $unreadNotifs = \App\Models\AppNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
@endphp
@endauth

{{-- NAVBAR (public/guest pages only) --}}
@if(!($isDashboard ?? false) || !auth()->check())
<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-blue-600 shrink-0">
                <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-bold">S</span>
                ServeMix
            </a>

            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <form action="{{ route('services.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Find services..."
                               class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </form>
            </div>

            <nav class="hidden md:flex items-center gap-4">
                <a href="{{ route('services.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Explore</a>
                @auth
                    <a href="{{ route('messages.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Messages</a>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition relative">
                        <i class="fas fa-bell"></i>
                        @if($unreadNotifs > 0)
                            <span class="absolute -top-2 -right-3 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ $unreadNotifs }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('register') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Become a Seller</a>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-full transition shadow-md shadow-blue-200">Join</a>
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
                    <a href="{{ $dashboardRoute }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-th-large mr-2 text-gray-400"></i>Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-user mr-2 text-gray-400"></i>Profile</a>
                    <a href="{{ route('wallet.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-wallet mr-2 text-gray-400"></i>Wallet</a>
                    <a href="{{ route('favorites.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-heart mr-2 text-red-400"></i>Favorites</a>
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
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
                <input type="text" name="q" placeholder="Find services..." class="w-full px-4 py-2 bg-gray-100 rounded-lg text-sm border-0 focus:ring-2 focus:ring-blue-500">
            </form>
            <a href="{{ route('services.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Explore</a>
            @auth
                <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                <a href="{{ route('messages.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Messages</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Logout</button></form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">Sign In</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-600 hover:bg-blue-50">Join</a>
            @endauth
        </div>
    </div>
</header>
@endif

{{-- FLASH MESSAGES --}}
<div class="fixed top-4 right-4 z-[100] space-y-2" x-data="{ msgs: {{ json_encode(array_values(array_filter([
    session('success') ? ['type'=>'success','text'=>session('success')] : null,
    session('info') ? ['type'=>'info','text'=>session('info')] : null,
    $errors->any() ? ['type'=>'error','text'=>$errors->first()] : null
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
            <span class="font-bold text-white text-sm">Admin Panel</span>
        @elseif($isProvider)
            <span class="font-bold text-gray-800 text-sm">Seller Dashboard</span>
        @else
            <span class="font-bold text-gray-800 text-sm">My Account</span>
        @endif
        <a href="{{ route('home') }}" class="ml-auto text-xs {{ $isAdmin ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-blue-600' }} transition">
            <i class="fas fa-home"></i>
        </a>
    </div>

    <aside class="w-64 shrink-0 {{ $isAdmin ? 'bg-slate-900 text-gray-300' : 'bg-white border-r border-gray-200 text-gray-700' }} flex flex-col fixed inset-y-0 left-0 z-50 transition-transform duration-300"
           :class="sideOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" id="sidebar">
        <div class="p-5 {{ $isAdmin ? 'border-b border-slate-700' : 'border-b border-gray-100' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($isAdmin)
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center"><i class="fas fa-shield-alt text-sm"></i></span>
                        <span class="font-bold text-white text-lg">Admin Panel</span>
                    @elseif($isProvider)
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-bold">S</span>
                        <div><div class="font-bold text-sm">Seller Dashboard</div></div>
                    @else
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-bold">S</span>
                        <div class="font-bold text-sm">My Account</div>
                    @endif
                </div>
                <button type="button" @click="sideOpen = false" class="lg:hidden {{ $isAdmin ? 'text-gray-400 hover:text-white' : 'text-gray-400 hover:text-gray-600' }} w-8 h-8 flex items-center justify-center rounded-lg transition hover:bg-black/10">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <a href="{{ route('home') }}" class="flex items-center gap-2 mt-4 text-sm {{ $isAdmin ? 'text-gray-400 hover:text-white' : 'text-gray-500 hover:text-blue-600' }} transition">
                <i class="fas fa-arrow-left text-xs"></i> Back to Home
            </a>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @if($isAdmin)
                @php $navLinks = [
                    ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
                    ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Users'],
                    ['route' => 'admin.services.index', 'icon' => 'fa-briefcase', 'label' => 'Services', 'badge' => $adminPendingCounts['services'] ?? 0],
                    ['route' => 'admin.categories.index', 'icon' => 'fa-tags', 'label' => 'Categories'],
                    ['route' => 'admin.orders.index', 'icon' => 'fa-box', 'label' => 'Orders'],
                    ['route' => 'admin.disputes.index', 'icon' => 'fa-exclamation-triangle', 'label' => 'Disputes', 'badge' => $adminPendingCounts['disputes'] ?? 0],
                    ['route' => 'admin.withdrawals.index', 'icon' => 'fa-money-bill-wave', 'label' => 'Withdrawals', 'badge' => $adminPendingCounts['withdrawals'] ?? 0],
                    ['route' => 'admin.reviews.index', 'icon' => 'fa-star', 'label' => 'Reviews'],
                    ['route' => 'admin.reports', 'icon' => 'fa-chart-bar', 'label' => 'Reports'],
                    ['route' => 'admin.customer-service.index', 'icon' => 'fa-headset', 'label' => 'Customer Service', 'badge' => $adminPendingCounts['cs'] ?? 0],
                ]; @endphp
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}" @click="sideOpen = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs($link['route'].'*') || request()->routeIs(str_replace('.index','',$link['route']).'.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas {{ $link['icon'] }} w-5 text-center"></i>{{ $link['label'] }}
                        @if(!empty($link['badge']))
                            <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $link['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            @elseif($isProvider)
                @php $navLinks = [
                    ['route' => 'provider.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Overview'],
                    ['route' => 'provider.orders.index', 'icon' => 'fa-box', 'label' => 'Orders'],
                    ['route' => 'provider.services.index', 'icon' => 'fa-briefcase', 'label' => 'My Services'],
                    ['route' => 'messages.index', 'icon' => 'fa-comment-dots', 'label' => 'Messages'],
                    ['route' => 'provider.withdraw.index', 'icon' => 'fa-money-bill-wave', 'label' => 'Withdrawals'],
                    ['route' => 'profile.edit', 'icon' => 'fa-cog', 'label' => 'Settings'],
                ]; @endphp
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}" @click="sideOpen = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs($link['route'].'*') || request()->routeIs(str_replace('.index','',$link['route']).'.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                        <i class="fas {{ $link['icon'] }} w-5 text-center"></i>{{ $link['label'] }}
                    </a>
                @endforeach
            @else
                @php $navLinks = [
                    ['route' => 'customer.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
                    ['route' => 'customer.orders.index', 'icon' => 'fa-box', 'label' => 'My Orders'],
                    ['route' => 'messages.index', 'icon' => 'fa-comment-dots', 'label' => 'Messages'],
                    ['route' => 'profile.edit', 'icon' => 'fa-cog', 'label' => 'Settings'],
                    ['route' => 'customer-service.index', 'icon' => 'fa-headset', 'label' => 'Support'],
                ]; @endphp
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}" @click="sideOpen = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs($link['route'].'*') || request()->routeIs(str_replace('.index','',$link['route']).'.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                        <i class="fas {{ $link['icon'] }} w-5 text-center"></i>{{ $link['label'] }}
                    </a>
                @endforeach
            @endif
        </nav>

        <div class="p-4 {{ $isAdmin ? 'border-t border-slate-700' : 'border-t border-gray-100' }}">
            <div class="flex items-center gap-3">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover">
                @else
                    <div class="w-9 h-9 rounded-full {{ $isAdmin ? 'bg-slate-700 text-gray-300' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="text-sm font-medium truncate {{ $isAdmin ? 'text-white' : 'text-gray-800' }}">{{ auth()->user()->name }}</div>
                    <div class="text-xs truncate {{ $isAdmin ? 'text-gray-500' : 'text-gray-400' }}">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 bg-gray-50 min-h-screen">
        <div class="p-4 pt-[4.5rem] sm:p-6 sm:pt-[5rem] lg:p-8 lg:pt-8">
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
