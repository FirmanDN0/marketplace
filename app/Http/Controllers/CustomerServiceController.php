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


    public function start()
    {
        $user = auth()->user();

        $conversation = CsConversation::create([
            'user_id' => $user->id,
            'subject' => 'Pusat Bantuan AI',
            'status'  => 'ai',
        ]);

        // Welcome message
        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => null,
            'sender_type'     => 'ai',
            'message'         => "Halo **{$user->name}**! 👋\n\nSaya AI Customer Service yang siap membantu Anda 24/7. Silakan pilih salah satu pertanyaan di bawah atau ketik pertanyaan Anda sendiri.",
        ]);

        return redirect()->route('customer-service.show', $conversation->id);
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
        // 1. Check Internal Templates First
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

        // 2. Fallback to Gemini AI
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
