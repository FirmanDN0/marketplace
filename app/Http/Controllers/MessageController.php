<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $user          = auth()->user();
        $conversations = Conversation::with(['lastMessage', 'customer', 'provider', 'service'])
            ->where('customer_id', $user->id)
            ->orWhere('provider_id', $user->id)
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $user = auth()->user();

        if ($conversation->customer_id !== $user->id && $conversation->provider_id !== $user->id) {
            abort(403);
        }

        // Mark incoming messages as read
        $unreadCount = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        if ($unreadCount > 0) {
            $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            try {
                broadcast(new \App\Events\MessageRead($conversation->id))->toOthers();
            } catch (\Throwable $e) {}
        }

        $messages = $conversation->messages()->with('sender')->get();
        $other    = $conversation->otherParticipant($user);

        return view('messages.show', compact('conversation', 'messages', 'other'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        if ($conversation->customer_id !== $user->id && $conversation->provider_id !== $user->id) {
            abort(403);
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

        Message::create([
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

        return back();
    }

    public function startOrFind(Request $request)
    {
        $data = $request->validate([
            'provider_id' => 'nullable|exists:users,id',
            'customer_id' => 'nullable|exists:users,id',
            'service_id'  => 'nullable|exists:services,id',
        ]);

        $me = auth()->user();

        if (!empty($data['provider_id'])) {
            // Current user is acting as the customer, messaging a provider
            $customerId = $me->id;
            $providerId = $data['provider_id'];
        } elseif (!empty($data['customer_id'])) {
            // Current user is acting as the provider, messaging a customer
            $customerId = $data['customer_id'];
            $providerId = $me->id;
        } else {
            $customerId = null;
            $providerId = null;
        }

        abort_if(!$customerId || !$providerId, 400, 'Missing participant ID.');

        $conversation = Conversation::firstOrCreate([
            'customer_id' => $customerId,
            'provider_id' => $providerId,
            'service_id'  => $data['service_id'] ?? null,
        ]);

        return redirect()->route('messages.show', $conversation->id);
    }

    public function destroy(Message $message)
    {
        $user = auth()->user();

        // Access check: only the sender can delete their message
        if ($message->sender_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete attachment if exists
        if ($message->attachment_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully.'
            ]);
        }

        return back()->with('success', 'Message deleted.');
    }
}
