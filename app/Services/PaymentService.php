<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    // Platform fee percentage (10%)
    const PLATFORM_FEE_PERCENT = 10;

    /**
     * Initiate a payment for an order (Midtrans-style simulation).
     */
    public function initiate(Order $order, string $method = 'midtrans'): Payment
    {
        return DB::transaction(function () use ($order, $method) {
            $payment = Payment::create([
                'order_id'       => $order->id,
                'user_id'        => $order->customer_id,
                'amount'         => $order->price,
                'payment_method' => $method,
                'status'         => 'pending',
                'payment_token'  => 'SIM-' . strtoupper(uniqid()),
                'payment_url'    => route('payment.show', $order->id),
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_initiated',
                'payload'    => ['method' => $method, 'amount' => $order->price],
            ]);

            return $payment;
        });
    }

    /**
     * Mark payment as successful and transition order to in_progress.
     */
    public function markSuccess(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status'  => 'success',
                'paid_at' => now(),
                'gateway_transaction_id' => 'TXN-' . strtoupper(uniqid()),
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

            $customerProfile->increment('total_spent', $payment->amount);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_success',
                'payload'    => ['order_status' => 'paid'],
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
     * Mark payment as failed.
     */
    public function markFailed(Payment $payment): void
    {
        $payment->update(['status' => 'failed']);

        PaymentLog::create([
            'payment_id' => $payment->id,
            'event'      => 'payment_failed',
            'payload'    => [],
        ]);
    }

    /**
     * Process a refund.
     */
    public function refund(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'refunded']);

            $order = $payment->order;
            $order->update(['status' => 'cancelled']);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'event'      => 'payment_refunded',
                'payload'    => [],
            ]);
        });
    }
}
