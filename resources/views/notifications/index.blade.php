@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        @php
            $previousUrl = url()->previous();
            $fallbackUrl = auth()->user()->isProvider() ? route('provider.dashboard') : route('customer.dashboard');
            // Jika user me-refresh halaman ini, previous URL akan sama dengan current URL.
            // Kita gunakan fallbackUrl untuk mencegah terjebak di loop halaman ini.
            $backUrl = ($previousUrl !== url()->current() && $previousUrl !== route('login')) ? $previousUrl : $fallbackUrl;
        @endphp
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-blue-600 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                <i class="fas fa-arrow-left text-gray-500 group-hover:text-blue-600"></i>
            </div>
            Kembali
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-400/20">
                <i class="fas fa-bell text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Notifikasi</h1>
                <p class="text-gray-500 text-sm font-medium mt-0.5">Tetap terkini dengan aktivitas terbaru Anda</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route('notifications.all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-bold transition inline-flex items-center gap-1.5">
                    <i class="fas fa-check-double"></i> Tandai semua dibaca
                </button>
            </form>
            @endif
        </div>
    </div>

    <div id="notif-container" class="bg-white rounded-3xl shadow-sm border border-gray-100/80 overflow-hidden divide-y divide-gray-50">
        @forelse($notifications as $notif)
            <div class="flex items-start gap-4 px-5 py-4 {{ $notif->read_at ? 'bg-white' : 'bg-blue-50/30' }} hover:bg-gray-50/50 transition rt-notif" data-notif-id="{{ $notif->id }}">
                <div class="w-10 h-10 rounded-full {{ $notif->read_at ? 'bg-gray-100 text-gray-400' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-900 text-sm {{ $notif->read_at ? 'font-medium' : 'font-bold' }}">{{ $notif->title }}</div>
                    <div class="text-sm text-gray-600 mt-0.5">{{ $notif->message }}</div>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <span class="text-xs text-gray-400"><i class="far fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}</span>
                        @if($notif->action_url)
                            <a href="{{ $notif->read_at ? $notif->action_url : route('notifications.read', $notif->id) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
                                View <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                        @if(!$notif->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-400 hover:text-blue-600 font-medium transition">Mark read</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center" id="notif-empty">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-bell text-2xl text-amber-300"></i></div>
                <h3 class="text-lg font-bold text-gray-700 mb-1">Semua sudah dibaca!</h3>
                <p class="text-gray-400 text-sm font-medium">Tidak ada notifikasi saat ini.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif

</div>

@push('scripts')
<script type="module">
(() => {
    const pollUrl    = '/api/realtime/notifications/poll';
    const container  = document.getElementById('notif-container');
    const emptyState = document.getElementById('notif-empty');

    let lastId  = {{ $notifications->isNotEmpty() ? $notifications->max('id') : 0 }};
    let polling = true;

    function renderNotification(notif) {
        if (emptyState) emptyState.remove();

        const el = document.createElement('div');
        el.className = 'flex items-start gap-4 px-5 py-4 bg-blue-50/30 hover:bg-gray-50/50 transition rt-notif';
        el.dataset.notifId = notif.id;
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        el.style.transition = 'opacity 0.4s, transform 0.4s';

        let actionHtml = '';
        if (notif.action_url) {
            actionHtml = `<a href="/notifications/${notif.id}/read" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">View <i class="fas fa-arrow-right text-[10px]"></i></a>`;
        }

        el.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-bell"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-bold text-gray-900 text-sm">${escapeHtml(notif.title)}</div>
                <div class="text-sm text-gray-600 mt-0.5">${escapeHtml(notif.message)}</div>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <span class="text-xs text-gray-400"><i class="far fa-clock mr-1"></i>${notif.time_ago}</span>
                    ${actionHtml}
                    <span class="text-xs text-emerald-600 font-medium inline-flex items-center gap-1"><i class="fas fa-sparkles text-[10px]"></i> Baru!</span>
                </div>
            </div>`;

        // Insert at top
        container.insertBefore(el, container.firstChild);

        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function poll() {
        if (!polling) return;
        try {
            const res = await fetch(`${pollUrl}?after=${lastId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.notifications && data.notifications.length > 0) {
                // Render new notifications (they come in desc order, reverse to insert in correct order)
                const sorted = [...data.notifications].sort((a, b) => a.id - b.id);
                sorted.forEach(notif => {
                    if (!document.querySelector(`[data-notif-id="${notif.id}"]`)) {
                        renderNotification(notif);
                    }
                });
                lastId = data.last_id;

                // Sync badge counts
                if (window.syncUiUnreadCounts) window.syncUiUnreadCounts(true);
            }
        } catch (e) { /* ignore */ }
    }

    if (window.Echo) {
        window.Echo.private('App.Models.User.' + {{ auth()->id() }})
            .listen('NotificationSent', (e) => {
                const notif = e.notification;
                // Add time_ago property since it's typically appended by the resource
                notif.time_ago = 'Baru saja';
                if (!document.querySelector(`[data-notif-id="${notif.id}"]`)) {
                    renderNotification(notif);
                    lastId = Math.max(lastId, notif.id);
                    if (window.syncUiUnreadCounts) window.syncUiUnreadCounts(true);
                }
            });
    } else {
        setInterval(poll, 5000);
    }

    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) poll();
    });
})();
</script>
@endpush
@endsection
