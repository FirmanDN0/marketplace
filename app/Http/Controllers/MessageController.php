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
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

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
            'attachment'   => 'nullable|file|max:10240',
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
            'provider_id' => 'required|exists:users,id',
            'service_id'  => 'nullable|exists:services,id',
        ]);

        $customer   = auth()->user();
        $providerId = (int) $data['provider_id'];
        $serviceId  = $data['service_id'] ?? null;

        $conversation = Conversation::firstOrCreate([
            'customer_id' => $customer->id,
            'provider_id' => $providerId,
            'service_id'  => $serviceId,
        ]);

        return redirect()->route('messages.show', $conversation->id);
    }
}
