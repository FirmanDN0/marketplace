<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\Transaction;
use App\Models\UserProfile;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Notification;

class TopUpController extends Controller
{
    /**
     * Map Midtrans transaction_status to app status.
     */
    private function mapMidtransStatus(string $txStatus, ?string $fraudStatus = null): string
    {
        if ($txStatus === 'capture') {
            return ($fraudStatus === 'challenge') ? 'pending' : 'success';
        }

        return match ($txStatus) {
            'settlement' => 'success',
            'expire'     => 'expired',
            'cancel', 'deny' => 'failed',
            default      => 'pending',
        };
    }

    /**
     * Sync all pending top-ups for a user by checking Midtrans API.
     */
    public static function syncPendingTopUps(int $userId): void
    {
        // Only sync top-ups that have been pending for more than 24 hours
        $pendingTopUps = TopUp::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        $controller = new self();

        foreach ($pendingTopUps as $topUp) {
            try {
                $statusObj = \Midtrans\Transaction::status($topUp->order_id);
                $statusArr = (array) $statusObj;
                $txStatus  = $statusArr['transaction_status'] ?? null;
                $payType   = $statusArr['payment_type'] ?? null;
                $fraud     = $statusArr['fraud_status'] ?? null;

                if ($txStatus) {
                    $appStatus = $controller->mapMidtransStatus($txStatus, $fraud);
                    $controller->processStatus($topUp, $appStatus, $payType, $statusArr);
                }
            } catch (\Exception $e) {
                // If Midtrans returns 404 (order not found after 24h), mark as expired
                if (str_contains($e->getMessage(), '404')) {
                    $controller->processStatus($topUp, 'expired', null, ['error' => 'Transaction not found in Midtrans']);
                }
            }
        }
    }

    /**
     * Show the top-up form.
     */
    public function create()
    {
        $user    = auth()->user();
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);
        return view('topup.create', compact('profile'));
    }

    /**
     * Full paginated top-up history page.
     */
    public function history(Request $request)
    {
        $user = auth()->user();

        // Auto-sync pending top-ups with Midtrans before displaying
        self::syncPendingTopUps($user->id);

        $query = TopUp::where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $topUps  = $query->latest()->paginate(15)->withQueryString();
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        return view('topup.history', compact('topUps', 'profile'));
    }

    /**
     * Create top-up record and get Custom Gateway Payment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1|max:100000000',
        ]);

        $user    = auth()->user();
        $orderId = 'TOPUP-' . $user->id . '-' . time();

        $topUp = TopUp::create([
            'user_id'  => $user->id,
            'order_id' => $orderId,
            'amount'   => $data['amount'],
            'status'   => 'pending',
        ]);

        // Call Custom Payment Gateway
        $apiKey = config('services.payment_gateway.api_key');
        $baseUrl = config('services.payment_gateway.url');

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-KEY' => $apiKey,
            'Accept' => 'application/json',
        ])->post("{$baseUrl}/api/v1/transactions", [
            'reference_id' => $orderId,
            'amount' => $data['amount'],
        ]);

        $qrisString = null;
        if ($response->successful()) {
            $responseData = $response->json('data');
            $qrisString = $responseData['qris_string'] ?? null;
            $topUp->update(['snap_token' => $qrisString]); // We'll save qris_string here to reuse the field
        } else {
            \Illuminate\Support\Facades\Log::error('Payment Gateway Error: ' . $response->body());
            return back()->with('error', 'Gagal membuat transaksi di Payment Gateway. Silakan coba lagi.');
        }

        return redirect()->route('wallet.topup.finish', $topUp->id);
    }

    /**
     * Finish / redirect page after Midtrans payment.
     */
    public function finish(Request $request, TopUp $topUp)
    {
        if ($topUp->user_id !== auth()->id()) {
            abort(403);
        }

        // Re-check status from Custom Payment Gateway API
        if ($topUp->isPending()) {
            try {
                $apiKey = config('services.payment_gateway.api_key');
                $baseUrl = config('services.payment_gateway.url');

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-API-KEY' => $apiKey,
                    'Accept' => 'application/json',
                ])->get("{$baseUrl}/api/v1/transactions/{$topUp->order_id}");

                if ($response->successful()) {
                    $data = $response->json('data');
                    $pgStatus = strtolower($data['status'] ?? '');

                    if (in_array($pgStatus, ['paid', 'success', 'settlement'])) {
                        $this->processStatus($topUp, 'success', 'custom_gateway', $data);
                    } elseif (in_array($pgStatus, ['expired', 'failed', 'cancelled'])) {
                        $this->processStatus($topUp, 'failed', null, $data);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Payment Gateway Status Check Error: ' . $e->getMessage());
            }
            $topUp->refresh();
        }

        return view('topup.finish', compact('topUp'));
    }

    /**
     * Midtrans server-to-server notification webhook.
     */
    public function notification(Request $request)
    {
        $payload     = $request->all();
        $serverKey   = config('services.midtrans.server_key');
        $orderId     = $payload['order_id'] ?? null;
        $statusCode  = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signature   = $payload['signature_key'] ?? null;

        // Verify Midtrans signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!$signature || !hash_equals($expectedSignature, $signature)) {
            return response('Invalid signature', 403);
        }

        $notification = new Notification();
        $txStatus     = $notification->transaction_status;
        $paymentType  = $notification->payment_type;
        $fraudStatus  = $notification->fraud_status ?? null;

        $topUp = TopUp::where('order_id', $orderId)->first();
        if (!$topUp) {
            return response('Not Found', 404);
        }

        $status = $this->mapMidtransStatus($txStatus, $fraudStatus);

        $this->processStatus($topUp, $status, $paymentType, $request->all());

        return response('OK', 200);
    }

    /**
     * Shared logic: update top-up status and credit balance on success.
     */
    private function processStatus(TopUp $topUp, string $status, ?string $paymentType, array $response): void
    {
        if ($topUp->isSuccess()) {
            return; // idempotent
        }

        DB::transaction(function () use ($topUp, $status, $paymentType, $response) {
            $topUp->update([
                'status'           => $status,
                'payment_type'     => $paymentType,
                'gateway_response' => $response,
                'paid_at'          => $status === 'success' ? now() : $topUp->paid_at,
            ]);

            if ($status === 'success') {
                $profile       = UserProfile::firstOrCreate(['user_id' => $topUp->user_id]);
                $balanceBefore = $profile->balance;

                $profile->increment('balance', (float) $topUp->amount);

                Transaction::create([
                    'user_id'        => $topUp->user_id,
                    'payment_id'     => null,
                    'type'           => 'adjustment',
                    'amount'         => $topUp->amount,
                    'balance_before' => $balanceBefore,
                    'balance_after'  => $balanceBefore + $topUp->amount,
                    'description'    => 'Top-up via E-Wallet Gateway (' . ($paymentType ?? 'online') . ')',
                    'reference_id'   => $topUp->order_id,
                ]);
            }
        });
    }
}
