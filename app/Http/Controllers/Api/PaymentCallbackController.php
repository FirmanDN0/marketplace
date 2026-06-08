<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Handle webhook dari Payment Gateway Custom.
     */
    public function handle(Request $request, \App\Services\PaymentService $paymentService)
    {
        $secretKey = config('services.payment_gateway.secret_key');
        
        // 1. Ambil signature dari header yang dikirim oleh SendWebhookJob
        $signatureHeader = $request->header('X-Callback-Signature');
        
        // 2. Ambil raw payload JSON
        $payload = $request->getContent();
        
        // 3. Buat HMAC Signature menggunakan Secret Key dari .env untuk validasi
        $generatedSignature = hash_hmac('sha256', $payload, $secretKey);
        
        // 4. Verifikasi apakah signature cocok
        if (!hash_equals($generatedSignature, (string)$signatureHeader)) {
            Log::warning('Webhook Payment Gateway: Invalid Signature!', [
                'header' => $signatureHeader,
                'generated' => $generatedSignature,
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }
        
        // 5. Signature valid, proses data transaksi
        $data = json_decode($payload, true);
        Log::info('Webhook Payment Gateway Diterima & Valid:', $data);
        
        $referenceId = $data['reference_id'] ?? null;
        $status = strtolower($data['status'] ?? '');
        $gatewayTransactionId = $data['transaction_id'] ?? null;
        
        $isSuccess = in_array($status, ['paid', 'success', 'settlement']);
        
        if (str_starts_with($referenceId, 'TOPUP-')) {
            // --- Proses TopUp ---
            $topUp = \App\Models\TopUp::where('order_id', $referenceId)->first();
            if ($topUp && $topUp->isPending()) {
                $appStatus = $isSuccess ? 'success' : 'failed';
                
                \Illuminate\Support\Facades\DB::transaction(function () use ($topUp, $appStatus, $data, $gatewayTransactionId) {
                    $topUp->update([
                        'status'           => $appStatus,
                        'payment_type'     => 'custom_gateway',
                        'gateway_response' => $data,
                        'paid_at'          => $appStatus === 'success' ? now() : null,
                    ]);

                    if ($appStatus === 'success') {
                        $profile = \App\Models\UserProfile::firstOrCreate(['user_id' => $topUp->user_id]);
                        $balanceBefore = $profile->balance;
                        $profile->increment('balance', (float) $topUp->amount);

                        \App\Models\Transaction::create([
                            'user_id'        => $topUp->user_id,
                            'payment_id'     => null,
                            'type'           => 'adjustment',
                            'amount'         => $topUp->amount,
                            'balance_before' => $balanceBefore,
                            'balance_after'  => $balanceBefore + $topUp->amount,
                            'description'    => 'Top-up via E-Wallet Gateway',
                            'reference_id'   => $topUp->order_id,
                        ]);
                    }
                });
            }
        } else {
            // --- Proses Pembayaran Order ---
            $order = \App\Models\Order::where('order_number', $referenceId)->first();
            if ($order && $order->payment) {
                if ($isSuccess) {
                    $paymentService->markSuccess($order->payment, 'custom_gateway', $gatewayTransactionId);
                } else {
                    $paymentService->markFailed($order->payment);
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Webhook processed successfully'
        ], 200);
    }
}
