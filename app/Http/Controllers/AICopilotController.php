<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;

class AICopilotController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Generate SEO Service Content (Title, Description, Tags)
     */
    public function generateService(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string|max:500'
        ]);

        $data = $this->gemini->generateServiceContent($request->keywords);

        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menghasilkan konten dengan AI. Coba lagi.'], 500);
    }

    /**
     * Generate Professional Reply for Review or Chat
     */
    public function generateReply(Request $request)
    {
        $request->validate([
            'context' => 'required|string|max:2000',
            'type'    => 'required|in:chat,review'
        ]);

        $reply = $this->gemini->generateReply($request->context, $request->type);

        if ($reply) {
            return response()->json([
                'success' => true,
                'reply' => trim($reply)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menghasilkan balasan dengan AI.'], 500);
    }
}
