<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Message;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(AppNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    public function markAllRead()
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function syncCounts(Request $request)
    {
        $userId = $request->user()->id;

        $unreadNotifications = AppNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $unreadMessages = Message::whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->whereHas('conversation', function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                    ->orWhere('provider_id', $userId);
            })
            ->count();

        return response()->json([
            'notifications' => $unreadNotifications,
            'messages' => $unreadMessages,
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
