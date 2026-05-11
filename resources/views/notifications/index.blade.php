@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-500 text-sm mt-1">Stay updated with your latest activities</p>
        </div>
        <div class="flex items-center gap-3">
            <span id="rt-live" class="text-xs text-green-500 font-medium flex items-center gap-1 opacity-0 transition-opacity duration-300">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
            </span>
            @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route('notifications.all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-medium transition inline-flex items-center gap-1.5">
                    <i class="fas fa-check-double"></i> Mark all as read
                </button>
            </form>
            @endif
        </div>
    </div>

    <div id="notif-container" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
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
                            <a href="{{ $notif->action_url }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
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
                <div class="text-5xl text-gray-300 mb-4"><i class="fas fa-bell"></i></div>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">All caught up!</h3>
                <p class="text-gray-500 text-sm">No notifications at the moment.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif

</div>

@push('scripts')
<script>
(() => {
    const pollUrl    = '/api/realtime/notifications/poll';
    const container  = document.getElementById('notif-container');
    const emptyState = document.getElementById('notif-empty');
    const rtLive     = document.getElementById('rt-live');

    let lastId  = {{ $notifications->isNotEmpty() ? $notifications->max('id') : 0 }};
    let polling = true;

    setTimeout(() => { rtLive.style.opacity = '1'; }, 500);

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
            actionHtml = `<a href="${notif.action_url}" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">View <i class="fas fa-arrow-right text-[10px]"></i></a>`;
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

    setInterval(poll, 5000);

    document.addEventListener('visibilitychange', () => {
        polling = document.visibilityState === 'visible';
        if (polling) poll();
    });
})();
</script>
@endpush
@endsection
