<?php

namespace App\Http\Controllers;

use App\Models\CsConversation;
use App\Models\CsMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $conversations = CsConversation::where('user_id', $user->id)
            ->with('lastMessage')
            ->latest()
            ->paginate(15);

        return view('customer-service.index', compact('conversations'));
    }

    public function create()
    {
        return view('customer-service.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        $user = auth()->user();

        $conversation = CsConversation::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'status'  => 'ai',
        ]);

        // Save user's first message
        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'sender_type'     => 'user',
            'message'         => $data['message'],
        ]);

        // Get AI response
        $this->handleAiReply($conversation, [], $data['message']);

        return redirect()->route('customer-service.show', $conversation->id)
            ->with('success', 'Percakapan berhasil dimulai!');
    }

    public function show(CsConversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            abort(403);
        }

        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        return view('customer-service.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, CsConversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            abort(403);
        }

        if ($conversation->isClosed()) {
            return back()->with('error', 'Percakapan ini sudah ditutup.');
        }

        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $user = auth()->user();

        // Fetch history BEFORE saving the new user message (to avoid duplication in AI context)
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

        // Only AI replies when conversation is in AI mode
        if ($conversation->isAi()) {
            $this->handleAiReply($conversation, $history, $data['message']);
        }

        return redirect()->route('customer-service.show', $conversation->id);
    }

    public function escalate(CsConversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            abort(403);
        }

        if ($conversation->isAi()) {
            $conversation->update(['status' => 'human']);
            CsMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => null,
                'sender_type'     => 'ai',
                'message'         => 'Anda telah meminta untuk berbicara dengan agen CS manusia. Kami akan segera menghubungkan Anda. Mohon tunggu sebentar.',
            ]);
        }

        return redirect()->route('customer-service.show', $conversation->id);
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function handleAiReply(CsConversation $conversation, array $history, string $userMessage): void
    {
        $gemini   = new GeminiService();
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
