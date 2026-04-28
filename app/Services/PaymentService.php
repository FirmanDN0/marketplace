<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;

class PaymentService
{
    // Platform fee percentage (10%)
    const PLATFORM_FEE_PERCENT = 10;

    /**
     * Map Midtrans transaction_status to app status.
     */
    public function mapMidtransStatus(string $txStatus, ?string $fraudStatus = null): string
    {
        if ($txStatus === 'capture') {
            return ($fraudStatus === 'challenge') ? 'pending' : 'success';
        }

        return match ($txStatus) {
            'settlement' => 'success',
            'expire'     => 'failed',
            'cancel', 'deny' => 'failed',
            default      => 'pending',
        };
    }

    /**
     * Initiate a payment for an order — generates a real Midtrans Snap token.
     */
    public function initiate(Order $order, string $method = 'midtrans'): Payment
    {
        return DB::transaction(function () use ($order, $method) {
            $params = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $order->price,
                ],
                'item_details' => [
                    [
                        'id'       => 'SVC-' . $order->service_id,
                        'price'    => (int) $order->price,
                        'quantity' => 1,
                        'name'     => substr(optional($order->service)->title ?? 'Service Order', 0, 50),
                    ],
                ],
                'customer_details' => [
                    'first_name' => $order->customer->name,
                    'email'      => $order->customer->email,
                ],
                'callbacks' => [
                    'finish' => route('payment.finish', $order->id),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $payment = Payment::create([
                'order_id'       => $order->id,
                'user_id'        => $order->customer_id,
                'amount'         => $order->price,
                'payment_method' => $method,
                'status'         => 'pending',
                'payment_token'  => $snapToken,
                'payment_url'    => route('payment.show', $order->id),
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_initiated',
                'payload'    => ['method' => $method, 'amount' => $order->price, 'snap_token' => $snapToken],
            ]);

            return $payment;
        });
    }

    /**
     * Mark payment as successful and transition order to paid.
     */
    public function markSuccess(Payment $payment, ?string $paymentType = null, ?string $transactionId = null): void
    {
        if ($payment->status === 'success') {
            return; // idempotent
        }

        DB::transaction(function () use ($payment, $paymentType, $transactionId) {
            $payment->update([
                'status'                 => 'success',
                'paid_at'                => now(),
                'payment_method'         => $paymentType ?? $payment->payment_method,
                'gateway_transaction_id' => $transactionId,
            ]);

            $order = $payment->order;
            $platformFee     = round($order->price * self::PLATFORM_FEE_PERCENT / 100, 2);
            $providerEarning = $order->price - $platformFee;

            $order->update([
                'status'           => 'paid',
                'platform_fee'     => $platformFee,
                'provider_earning' => $providerEarning,
            ]);

            // Customer transaction record
            $customerProfile = UserProfile::firstOrCreate(['user_id' => $order->customer_id]);
            Transaction::create([
                'user_id'        => $order->customer_id,
                'payment_id'     => $payment->id,
                'type'           => 'payment',
                'amount'         => -$payment->amount,
                'balance_before' => $customerProfile->balance,
                'balance_after'  => $customerProfile->balance,
                'description'    => "Payment for Order #{$order->order_number}",
                'reference_id'   => $order->order_number,
            ]);

            $customerProfile->increment('total_spent', (float) $payment->amount);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_success',
                'payload'    => ['order_status' => 'paid', 'payment_type' => $paymentType],
            ]);

            // Notify provider
            NotificationService::send(
                $order->provider_id,
                'new_order',
                'New Order Received',
                "You have a new order #{$order->order_number}.",
                ['order_id' => $order->id],
                route('provider.orders.show', $order->id)
            );
        });
    }

    /**
     * Pay for an order using wallet balance (instant).
     */
    public function payWithWallet(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {
            $customerProfile = UserProfile::firstOrCreate(['user_id' => $order->customer_id]);

            if ($customerProfile->balance < $order->price) {
                throw new \Exception('Saldo tidak mencukupi.');
            }

            $balanceBefore = $customerProfile->balance;
            $balanceAfter  = $balanceBefore - $order->price;

            // Deduct balance
            $customerProfile->update(['balance' => $balanceAfter]);
            $customerProfile->increment('total_spent', (float) $order->price);

            // Create payment record
            $payment = Payment::create([
                'order_id'               => $order->id,
                'user_id'                => $order->customer_id,
                'amount'                 => $order->price,
                'payment_method'         => 'balance',
                'status'                 => 'success',
                'paid_at'                => now(),
                'gateway_transaction_id' => 'WALLET-' . $order->order_number,
            ]);

            // Calculate fees
            $platformFee     = round($order->price * self::PLATFORM_FEE_PERCENT / 100, 2);
            $providerEarning = $order->price - $platformFee;

            $order->update([
                'status'           => 'paid',
                'platform_fee'     => $platformFee,
                'provider_earning' => $providerEarning,
            ]);

            // Transaction record
            Transaction::create([
                'user_id'        => $order->customer_id,
                'payment_id'     => $payment->id,
                'type'           => 'payment',
                'amount'         => -$order->price,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => "Pembayaran saldo untuk Order #{$order->order_number}",
                'reference_id'   => $order->order_number,
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_wallet_success',
                'payload'    => ['balance_before' => $balanceBefore, 'balance_after' => $balanceAfter],
            ]);

            // Notify provider
            NotificationService::send(
                $order->provider_id,
                'new_order',
                'New Order Received',
                "You have a new order #{$order->order_number}.",
                ['order_id' => $order->id],
                route('provider.orders.show', $order->id)
            );

            return $payment;
        });
    }

    /**
     * Mark payment as failed.
     */
    public function markFailed(Payment $payment): void
    {
        if ($payment->status === 'success') {
            return; // don't overwrite success
        }

        $payment->update(['status' => 'failed']);

        PaymentLog::create([
            'payment_id' => $payment->id,
            'event'      => 'payment_failed',
            'payload'    => [],
        ]);
    }

    /**
     * Process a refund — return funds to customer wallet balance.
     */
    public function refund(Payment $payment): void
    {
        if ($payment->status === 'refunded') {
            return;
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'refunded']);

            $order = $payment->order;
            $order->update(['status' => 'cancelled']);

            // Refund to customer wallet balance
            $customerProfile = UserProfile::firstOrCreate(['user_id' => $payment->user_id]);
            $balanceBefore = $customerProfile->balance;
            $balanceAfter  = $balanceBefore + $payment->amount;

            $customerProfile->update(['balance' => $balanceAfter]);
            $customerProfile->decrement('total_spent', (float) $payment->amount);

            Transaction::create([
                'user_id'        => $payment->user_id,
                'payment_id'     => $payment->id,
                'type'           => 'refund',
                'amount'         => $payment->amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => "Refund untuk Order #{$order->order_number}",
                'reference_id'   => $order->order_number,
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_refunded',
                'payload'    => ['refund_amount' => $payment->amount, 'balance_after' => $balanceAfter],
            ]);

            // Notify customer
            NotificationService::send(
                $payment->user_id,
                'payment_refunded',
                'Pembayaran Direfund',
                "Pembayaran Rp " . number_format($payment->amount, 0, ',', '.') . " untuk Order #{$order->order_number} telah dikembalikan ke saldo kamu.",
                ['order_id' => $order->id],
                route('wallet.index')
            );
        });
    }
}
