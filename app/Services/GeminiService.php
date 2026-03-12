<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    /** Models tried in order when a previous one hits quota/rate limits */
    private array $models = [
        'gemini-2.5-flash-lite',
        'gemini-2.0-flash-lite',
        'gemini-2.5-flash',
    ];
    private string $systemPrompt;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');

        $this->systemPrompt = <<<EOT
Kamu adalah AI Customer Service Marketplace-Platform, marketplace jasa digital (desain, konten, programming, dll.) yang menghubungkan customer dan provider.

Kepribadian: ramah, hangat, santai tapi profesional, boleh pakai emoji. Jawab selalu dalam Bahasa Indonesia yang natural.

Pengetahuan platform:
- Order: browse layanan → pilih paket → bayar via wallet → provider kerjakan → customer terima atau dispute → beri ulasan
- Wallet: top-up via Midtrans (bank/VA/QRIS/e-wallet), bayar order, provider bisa withdraw setelah order selesai, dana escrow selama order berlangsung
- Status order: Pending (menunggu konfirmasi) → Active (dikerjakan) → Delivered (dikirim, menunggu terima) → Completed / Cancelled / Disputed
- Dispute: ajukan jika tidak puas, CS mediasi, resolusi berupa refund atau revisi
- Review: rating 1-5 bintang + komentar setelah order selesai, provider bisa balas
- Provider: buat profil, listing layanan dengan beberapa paket (basic/standard/premium), upload portofolio, penghasilan dipotong komisi platform
- Notifikasi: cek ikon lonceng di navbar
- Keamanan: jangan bagikan password/OTP ke siapapun, CS resmi tidak pernah minta password

Aturan menjawab:
- HANYA jawab pertanyaan yang berkaitan dengan platform marketplace ini (order, pembayaran, wallet, top-up, withdraw, layanan, dispute, review, akun, keamanan, cara penggunaan fitur, dll.)
- Jika pengguna bertanya di luar topik platform (misalnya politik, pengetahuan umum, hiburan, dll.), tolak dengan sopan dan arahkan kembali ke topik platform. Contoh: "Maaf, saya hanya bisa membantu pertanyaan seputar marketplace ini ya 😊 Ada yang bisa saya bantu terkait layanan, pesanan, atau wallet kamu?"
- Jika pertanyaan ambigu/singkat, tebak maksudnya dalam konteks platform dan jawab, lalu tawarkan klarifikasi
- Jangan langsung eskalasi hanya karena pertanyaan sulit atau singkat
- Eskalasi ke CS manusia HANYA jika diminta secara eksplisit oleh pengguna
- Format eskalasi: [ESCALATE] <pesan hangat>
EOT;
    }

    /**
     * Send a message and get AI response, including conversation history for context.
     *
     * @param  array  $history  Array of ['sender_type' => 'user'|'ai', 'message' => '...']
     * @param  string $userMessage  The new user message
     * @return string AI response text
     */
    public function chat(array $history, string $userMessage): string
    {
        if (empty($this->apiKey)) {
            Log::error('GeminiService: API key is empty');
            return 'Maaf, layanan AI sedang tidak tersedia. Silakan coba lagi atau ketik "minta cs manusia" untuk berbicara dengan agen kami.';
        }

        $contents = [];

        // Build conversation history (exclude agent messages from AI context)
        foreach ($history as $msg) {
            if ($msg['sender_type'] === 'user') {
                $contents[] = ['role' => 'user', 'parts' => [['text' => $msg['message']]]];
            } elseif ($msg['sender_type'] === 'ai') {
                $contents[] = ['role' => 'model', 'parts' => [['text' => $msg['message']]]];
            }
        }

        // Add new user message
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $this->systemPrompt]],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'maxOutputTokens' => 800,
                'temperature'     => 0.85,
            ],
        ];

        try {
            // Try each model in order; move to next on 429 (quota exceeded)
            foreach ($this->models as $model) {
                $response = Http::asJson()
                    ->timeout(30)
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}",
                        $payload
                    );

                if ($response->status() === 429 || $response->status() === 404) {
                    Log::warning("GeminiService: skipping {$model} (status {$response->status()}), trying next model");
                    continue; // try next model
                }

                if ($response->failed()) {
                    Log::error('GeminiService: API request failed', [
                        'model'  => $model,
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                    return 'Maaf, saya sedang mengalami gangguan teknis. Silakan coba lagi beberapa saat.';
                }

                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$text) {
                    Log::warning('GeminiService: Unexpected response structure', [
                        'model' => $model,
                        'data'  => $response->json(),
                    ]);
                    return 'Maaf, saya tidak bisa memproses permintaan Anda saat ini. Silakan coba lagi.';
                }

                return $text;
            }

            // All models exhausted
            Log::error('GeminiService: All models quota exhausted');
            return 'Maaf, layanan AI sedang tidak tersedia sementara karena batas penggunaan harian telah tercapai. Silakan ketik "minta cs manusia" untuk berbicara langsung dengan agen kami. 🙏';

        } catch (\Exception $e) {
            Log::error('GeminiService: Exception', ['message' => $e->getMessage()]);
            return 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi beberapa saat.';
        }
    }

    /**
     * Determine whether the USER message signals an escalation to human.
     * AI-side [ESCALATE] is also checked, but only as a secondary signal.
     */
    public function shouldEscalate(string $aiResponse, string $userMessage): bool
    {
        // AI explicitly flagged escalation
        if (str_starts_with(trim($aiResponse), '[ESCALATE]')) {
            return true;
        }

        // Check user message for explicit human-request keywords
        $keywords = [
            'cs manusia', 'customer service manusia', 'manusia saja', 'bicara dengan manusia',
            'agen manusia', 'hubungkan ke manusia', 'minta disambungkan', 'human agent',
            'tolong bantu manusia', 'cs asli', 'langsung dengan orangnya', 'minta cs manusia',
            'berbicara dengan orang', 'chat dengan orang', 'manusia langsung',
            'hubungi manusia', 'mau ke manusia', 'ke cs manusia', 'cs human',
        ];

        $lower = strtolower($userMessage);
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Strip the [ESCALATE] tag from an AI response.
     */
    public function cleanEscalateTag(string $message): string
    {
        return trim(preg_replace('/^\[ESCALATE\]\s*/i', '', $message));
    }
}

