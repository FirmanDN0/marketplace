@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-500 text-sm mt-1">Stay updated with your latest activities</p>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
        <form method="POST" action="{{ route('notifications.all-read') }}">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-medium transition inline-flex items-center gap-1.5">
                <i class="fas fa-check-double"></i> Mark all as read
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
        @forelse($notifications as $notif)
            <div class="flex items-start gap-4 px-5 py-4 {{ $notif->read_at ? 'bg-white' : 'bg-blue-50/30' }} hover:bg-gray-50/50 transition">
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
            <div class="py-16 text-center">
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
@endsection
