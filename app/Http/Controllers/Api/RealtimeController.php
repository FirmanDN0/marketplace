<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Conversation;
use App\Models\CsConversation;
use App\Models\CsMessage;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RealtimeController extends Controller
{
    // ─── Private Messages ─────────────────────────────────────────────

    /**
     * Fetch new messages in a conversation after a given message ID.
     */
    public function messagesPoll(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->customer_id !== $user->id && $conversation->provider_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $afterId = (int) $request->query('after', 0);

        // Mark incoming messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $newMessages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(function ($msg) use ($user) {
                return [
                    'id'              => $msg->id,
                    'mine'            => $msg->sender_id === $user->id,
                    'sender_name'     => optional($msg->sender)->name ?? 'Unknown',
                    'message_text'    => $msg->message_text,
                    'attachment_path' => $msg->attachment_path ? Storage::url($msg->attachment_path) : null,
                    'attachment_name' => $msg->attachment_name,
                    'time'            => $msg->created_at->format('d M H:i'),
                ];
            });

        return response()->json([
            'messages'  => $newMessages,
            'last_id'   => $newMessages->isNotEmpty() ? $newMessages->last()['id'] : $afterId,
        ]);
    }

    /**
     * Send a message via AJAX (no page reload).
     */
    public function messagesSend(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->customer_id !== $user->id && $conversation->provider_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'message_text' => 'required_without:attachment|nullable|string|max:5000',
            'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar,txt,csv|max:10240',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentPath = $file->store('message-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
        }

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message_text'    => $data['message_text'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $receiverId = $conversation->customer_id === $user->id
            ? $conversation->provider_id
            : $conversation->customer_id;

        \App\Services\NotificationService::send(
            $receiverId,
            'new_message',
            'New Message',
            "{$user->name} sent you a message.",
            ['conversation_id' => $conversation->id],
            route('messages.show', $conversation->id)
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id'              => $msg->id,
                'mine'            => true,
                'sender_name'     => $user->name,
                'message_text'    => $msg->message_text,
                'attachment_path' => $attachmentPath ? Storage::url($attachmentPath) : null,
                'attachment_name' => $attachmentName,
                'time'            => $msg->created_at->format('d M H:i'),
            ],
        ]);
    }

    // ─── Customer Service Chat ────────────────────────────────────────

    /**
     * Poll CS conversation for new messages (user side).
     */
    public function csPoll(Request $request, CsConversation $conversation)
    {
        $user = $request->user();

        if ($conversation->user_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $afterId = (int) $request->query('after', 0);

        $newMessages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->filter(fn($m) => trim($m->message) !== '')
            ->map(function ($msg) use ($user) {
                return [
                    'id'          => $msg->id,
                    'is_user'     => $msg->isUser(),
                    'is_ai'       => $msg->isAi(),
                    'is_agent'    => $msg->isAgent(),
                    'sender_name' => $msg->isAi() ? 'AI Assistant' : ($msg->isAgent() ? optional($msg->sender)->name ?? 'Agent' : $user->name),
                    'message'     => $this->formatCsMessage($msg->message),
                    'time'        => $msg->created_at->format('H:i'),
                    'date'        => $msg->created_at->format('Y-m-d'),
                ];
            })
            ->values();

        return response()->json([
            'messages'    => $newMessages,
            'last_id'     => $newMessages->isNotEmpty() ? $newMessages->last()['id'] : $afterId,
            'status'      => $conversation->fresh()->status,
        ]);
    }

    /**
     * Send CS message via AJAX (user side).
     */
    public function csSend(Request $request, CsConversation $conversation)
    {
        $user = $request->user();

        if ($conversation->user_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if ($conversation->isClosed()) {
            return response()->json(['error' => 'Percakapan ini sudah ditutup.'], 422);
        }

        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        // Fetch history BEFORE saving the new user message
        $history = [];
        if ($conversation->isAi()) {
            $history = $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->map(fn($m) => ['sender_type' => $m->sender_type, 'message' => $m->message])
                ->toArray();
        }

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'sender_type'     => 'user',
            'message'         => $data['message'],
        ]);

        // AI reply when conversation is in AI mode
        if ($conversation->isAi()) {
            $this->handleAiReply($conversation, $history, $data['message']);
        }

        // Fetch all new messages after the request
        $afterId = (int) $request->query('after', 0);
        $allNew = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->filter(fn($m) => trim($m->message) !== '')
            ->map(function ($msg) use ($user) {
                return [
                    'id'          => $msg->id,
                    'is_user'     => $msg->isUser(),
                    'is_ai'       => $msg->isAi(),
                    'is_agent'    => $msg->isAgent(),
                    'sender_name' => $msg->isAi() ? 'AI Assistant' : ($msg->isAgent() ? optional($msg->sender)->name ?? 'Agent' : $user->name),
                    'message'     => $this->formatCsMessage($msg->message),
                    'time'        => $msg->created_at->format('H:i'),
                    'date'        => $msg->created_at->format('Y-m-d'),
                ];
            })
            ->values();

        return response()->json([
            'success'  => true,
            'messages' => $allNew,
            'last_id'  => $allNew->isNotEmpty() ? $allNew->last()['id'] : $afterId,
            'status'   => $conversation->fresh()->status,
        ]);
    }

    /**
     * Poll CS conversation for new messages (admin side).
     */
    public function csAdminPoll(Request $request, CsConversation $conversation)
    {
        $afterId = (int) $request->query('after', 0);

        $newMessages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(function ($msg) use ($conversation) {
                return [
                    'id'          => $msg->id,
                    'is_user'     => $msg->isUser(),
                    'is_ai'       => $msg->isAi(),
                    'is_agent'    => $msg->isAgent(),
                    'sender_name' => $msg->isAi() ? 'AI Assistant' : ($msg->isAgent() ? optional($msg->sender)->name ?? 'Admin' : optional($conversation->user)->name ?? 'User'),
                    'sender_initial' => strtoupper(substr(optional($conversation->user)->name ?? 'U', 0, 1)),
                    'message'     => $this->formatCsMessage($msg->message),
                    'time'        => $msg->created_at->format('H:i'),
                    'date'        => $msg->created_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'messages' => $newMessages,
            'last_id'  => $newMessages->isNotEmpty() ? $newMessages->last()['id'] : $afterId,
            'status'   => $conversation->fresh()->status,
        ]);
    }

    /**
     * Send CS message via AJAX (admin side).
     */
    public function csAdminSend(Request $request, CsConversation $conversation)
    {
        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $agent = $request->user();

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $agent->id,
            'sender_type'     => 'agent',
            'message'         => $data['message'],
        ]);

        $updates = [];
        if ($conversation->status !== 'human') {
            $updates['status'] = 'human';
        }
        if (!$conversation->agent_id) {
            $updates['agent_id'] = $agent->id;
        }
        if ($updates) {
            $conversation->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => CsMessage::where('conversation_id', $conversation->id)->latest()->first()->id,
                'is_user'     => false,
                'is_ai'       => false,
                'is_agent'    => true,
                'sender_name' => $agent->name,
                'sender_initial' => strtoupper(substr(optional($conversation->user)->name ?? 'U', 0, 1)),
                'message'     => $this->formatCsMessage($data['message']),
                'time'        => now()->format('H:i'),
                'date'        => now()->format('Y-m-d'),
            ],
        ]);
    }

    // ─── Notifications ────────────────────────────────────────────────

    /**
     * Poll for new notifications.
     */
    public function notificationsPoll(Request $request)
    {
        $user   = $request->user();
        $afterId = (int) $request->query('after', 0);

        $newNotifs = AppNotification::where('user_id', $user->id)
            ->where('id', '>', $afterId)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'type'       => $n->type,
                    'action_url' => $n->action_url,
                    'read_at'    => $n->read_at,
                    'time_ago'   => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at->toIso8601String(),
                ];
            });

        $unreadCount = AppNotification::where('user_id', $user->id)->whereNull('read_at')->count();

        return response()->json([
            'notifications' => $newNotifs,
            'unread_count'  => $unreadCount,
            'last_id'       => $newNotifs->isNotEmpty() ? $newNotifs->max('id') : $afterId,
        ]);
    }

    // ─── Dashboard Stats ──────────────────────────────────────────────

    /**
     * Real-time dashboard stats for provider/customer.
     */
    public function dashboardStats(Request $request)
    {
        $user = $request->user();

        if ($user->isProvider()) {
            $stats = [
                'total_services'   => \App\Models\Service::where('provider_id', $user->id)->count(),
                'active_services'  => \App\Models\Service::where('provider_id', $user->id)->where('status', 'active')->count(),
                'total_orders'     => Order::where('provider_id', $user->id)->count(),
                'active_orders'    => Order::where('provider_id', $user->id)->whereIn('status', ['paid', 'in_progress', 'delivered'])->count(),
                'completed_orders' => Order::where('provider_id', $user->id)->where('status', 'completed')->count(),
                'balance'          => optional($user->profile)->balance ?? 0,
                'total_earned'     => optional($user->profile)->total_earned ?? 0,
            ];
        } elseif ($user->isAdmin()) {
            $stats = [
                'total_users'      => \App\Models\User::count(),
                'total_orders'     => Order::count(),
                'pending_orders'   => Order::whereIn('status', ['paid', 'in_progress'])->count(),
                'total_services'   => \App\Models\Service::count(),
            ];
        } else {
            $stats = [
                'total_orders'     => Order::where('customer_id', $user->id)->count(),
                'active_orders'    => Order::where('customer_id', $user->id)->whereIn('status', ['paid', 'in_progress', 'delivered'])->count(),
                'completed_orders' => Order::where('customer_id', $user->id)->where('status', 'completed')->count(),
                'balance'          => optional($user->profile)->balance ?? 0,
                'total_spent'      => optional($user->profile)->total_spent ?? 0,
            ];
        }

        return response()->json(['stats' => $stats]);
    }

    // ─── Order Status ─────────────────────────────────────────────────

    /**
     * Poll order status for live tracking.
     */
    public function orderStatus(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->customer_id !== $user->id && $order->provider_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json([
            'status'            => $order->status,
            'delivered_at'      => optional($order->delivered_at)->toIso8601String(),
            'completed_at'      => optional($order->completed_at)->toIso8601String(),
            'cancelled_at'      => optional($order->cancelled_at)->toIso8601String(),
            'revision_count'    => $order->revision_count,
            'delivery_deadline' => optional($order->delivery_deadline)->toIso8601String(),
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function formatCsMessage(string $message): string
    {
        $escaped = e(trim($message));
        return preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $escaped);
    }

    /**
     * Handle AI reply for CS chat (duplicated from CustomerServiceController for AJAX path).
     */
    private function handleAiReply(CsConversation $conversation, array $history, string $userMessage): void
    {
        $templates = [
            'bagaimana cara memesan layanan' => 'Untuk memesan layanan, Anda dapat menelusuri kategori yang tersedia di beranda, pilih layanan yang Anda inginkan, pilih paket (Basic/Standard/Premium), lalu klik tombol **Pesan Sekarang**. Setelah itu, Anda akan diarahkan ke halaman pembayaran.',
            'bagaimana cara mengisi saldo wallet' => 'Anda dapat mengisi saldo wallet dengan cara klik ikon **Dompet** di navbar, lalu pilih menu **Top Up**. Masukkan jumlah saldo yang diinginkan dan lakukan pembayaran melalui metode yang tersedia (VA Bank, QRIS, atau E-wallet).',
            'apa itu dana escrow' => 'Dana escrow adalah sistem keamanan kami di mana dana pembayaran Anda akan ditahan sementara oleh platform. Dana tersebut baru akan diteruskan ke provider setelah Anda mengonfirmasi bahwa pekerjaan telah selesai dan sesuai dengan keinginan Anda.',
            'bagaimana jika hasil pekerjaan tidak sesuai' => 'Jika hasil pekerjaan tidak sesuai dengan kesepakatan awal, Anda dapat mengajukan **Dispute** pada halaman detail pesanan. Tim Customer Service kami akan membantu memediasi antara Anda dan provider untuk menemukan solusi terbaik (revisi atau refund).',
            'cara mendaftar jadi provider' => 'Untuk menjadi provider, silakan buka menu profil Anda dan pilih **Daftar sebagai Provider**. Anda akan diminta melengkapi data diri, keahlian, dan portofolio sebelum dapat mulai menjual layanan.',
        ];

        $lowerMessage = strtolower(trim($userMessage));
        $cleanedMessage = preg_replace('/[^a-z0-9 ]/', '', $lowerMessage);

        foreach ($templates as $trigger => $reply) {
            if (str_contains($cleanedMessage, strtolower($trigger))) {
                CsMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => null,
                    'sender_type'     => 'ai',
                    'message'         => $reply,
                ]);
                return;
            }
        }

        // Fallback to Gemini AI
        $gemini   = new \App\Services\GeminiService();
        $aiText   = $gemini->chat($history, $userMessage);
        $escalate = $gemini->shouldEscalate($aiText, $userMessage);
        $cleaned  = trim(str_replace(["\r\n", "\r"], "\n", $gemini->cleanEscalateTag($aiText)));

        if (empty(trim($cleaned))) {
            $cleaned = $escalate
                ? 'Baik, saya akan menghubungkan Anda dengan agen CS kami. Mohon tunggu sebentar.'
                : 'Maaf, saya tidak dapat memproses permintaan Anda saat ini. Silakan coba lagi beberapa saat.';
        }

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => null,
            'sender_type'     => 'ai',
            'message'         => $cleaned,
        ]);

        if ($escalate) {
            $conversation->update(['status' => 'human']);
        }
    }
}
