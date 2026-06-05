<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Transaction;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order (status: pending_payment).
     */
    public function create(int $customerId, Service $service, ServicePackage $package, ?string $notes = null, ?\App\Models\Voucher $voucher = null): Order
    {
        return DB::transaction(function () use ($customerId, $service, $package, $notes, $voucher) {
            $price = $package->price;
            $tax_fee = $price * 0.10; // 10% Biaya Layanan/PPN dibebankan ke pembeli
            $discount = 0;
            $voucher_id = null;

            if ($voucher && $voucher->isValidFor($price)) {
                $discount = $voucher->calculateDiscount($price);
                $voucher_id = $voucher->id;
                $voucher->increment('used_count');
            }

            $grand_total = max(0, $price + $tax_fee - $discount);

            $order = Order::create([
                'customer_id'     => $customerId,
                'provider_id'     => $service->provider_id,
                'service_id'      => $service->id,
                'package_id'      => $package->id,
                'price'           => $price,
                'tax_fee'         => $tax_fee,
                'discount'        => $discount,
                'grand_total'     => $grand_total,
                'voucher_id'      => $voucher_id,
                'status'          => 'pending_payment',
                'delivery_deadline' => null, // Deadline starts when requirements are submitted
                'notes'           => $notes,
            ]);

            return $order;
        });
    }

    /**
     * Create a new order from a custom offer (status: pending_payment).
     */
    public function createFromCustomOffer(\App\Models\CustomOffer $customOffer): Order
    {
        return DB::transaction(function () use ($customOffer) {
            $price = $customOffer->price;
            $tax_fee = $price * 0.10;
            $grand_total = $price + $tax_fee; // Custom Offer tidak bisa pakai voucher untuk saat ini

            $order = Order::create([
                'customer_id'       => $customOffer->customer_id,
                'provider_id'       => $customOffer->provider_id,
                'service_id'        => $customOffer->service_id ?? \App\Models\Service::first()->id, // Fallback if no service is linked
                'package_id'        => null,
                'custom_offer_id'   => $customOffer->id,
                'price'             => $price,
                'tax_fee'           => $tax_fee,
                'discount'          => 0,
                'grand_total'       => $grand_total,
                'status'            => 'pending_payment',
                'delivery_deadline' => null,
                'notes'             => $customOffer->description,
            ]);

            return $order;
        });
    }

    /**
     * Provider delivers the order.
     */
    public function deliver(Order $order, string $message, ?string $filePath = null): void
    {
        $order->update([
            'status'           => 'delivered',
            'delivery_message' => $message,
            'delivery_file'    => $filePath,
            'delivered_at'     => now(),
        ]);

        NotificationService::send(
            $order->customer_id,
            'order_delivered',
            'Order Delivered',
            "Your order #{$order->order_number} has been delivered. Please review it.",
            ['order_id' => $order->id],
            route('customer.orders.show', $order->id)
        );
    }

    /**
     * Customer requests a revision on a delivered order.
     */
    public function requestRevision(Order $order, string $message): void
    {
        $order->update([
            'status'                 => 'in_progress',
            'revision_count'         => $order->revision_count + 1,
            'revision_message'       => $message,
            'revision_requested_at'  => now(),
            // clear previous delivery so provider re-submits
            'delivery_message'       => null,
            'delivery_file'          => null,
            'delivered_at'           => null,
        ]);

        NotificationService::send(
            $order->provider_id,
            'order_revision_requested',
            'Revision Requested',
            "Customer requested a revision for order #{$order->order_number}.",
            ['order_id' => $order->id],
            route('provider.orders.show', $order->id)
        );
    }

    /**
     * Customer accepts the delivery and completes the order.
     */
    public function complete(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            $order->service()->increment('total_orders');

            // Credit provider balance (Mendapat UANG UTUH dari price, tidak ada potongan lagi)
            $profile = UserProfile::firstOrCreate(['user_id' => $order->provider_id]);
            $balanceBefore = $profile->balance;
            $profile->increment('balance', $order->price);
            $profile->increment('total_earned', $order->price);

            Transaction::create([
                'user_id'        => $order->provider_id,
                'payment_id'     => optional($order->payment)->id,
                'type'           => 'earning',
                'amount'         => $order->price,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $order->price,
                'description'    => "Earning for Order #{$order->order_number}",
                'reference_id'   => $order->order_number,
            ]);

            // Record tax_fee as platform revenue
            Transaction::create([
                'user_id'        => $order->provider_id,
                'payment_id'     => optional($order->payment)->id,
                'type'           => 'fee',
                'amount'         => $order->tax_fee,
                'balance_before' => 0,
                'balance_after'  => 0,
                'description'    => "Platform Service Fee (10%) for Order #{$order->order_number}",
                'reference_id'   => $order->order_number,
            ]);

            NotificationService::send(
                $order->provider_id,
                'order_completed',
                'Order Completed',
                "Order #{$order->order_number} has been completed. Earnings credited.",
                ['order_id' => $order->id],
                route('provider.orders.show', $order->id)
            );
        });
    }

    /**
     * Cancel an order. If already paid, refund to customer wallet.
     */
    public function cancel(Order $order, int $cancelledBy, string $reason): void
    {
        // If order was paid, process refund to wallet
        $payment = $order->payment;
        if ($payment && $payment->status === 'success' && in_array($order->status, ['paid', 'in_progress', 'delivered'])) {
            (new PaymentService())->refund($payment);
        } else {
            $order->update([
                'status'           => 'cancelled',
                'cancelled_by'     => $cancelledBy,
                'cancelled_reason' => $reason,
                'cancelled_at'     => now(),
            ]);
        }

        // Update cancel metadata if refund already set status
        if ($order->cancelled_by === null) {
            $order->update([
                'cancelled_by'     => $cancelledBy,
                'cancelled_reason' => $reason,
                'cancelled_at'     => now(),
            ]);
        }

        NotificationService::send(
            $order->customer_id === $cancelledBy ? $order->provider_id : $order->customer_id,
            'order_cancelled',
            'Order Cancelled',
            "Order #{$order->order_number} has been cancelled.",
            ['order_id' => $order->id]
        );
    }

    /**
     * Recalculate service avg rating after review.
     */
    public function updateServiceRating(int $serviceId): void
    {
        $service = Service::find($serviceId);
        if (!$service) return;

        $stats = $service->reviews()->selectRaw('COUNT(*) as cnt, AVG(rating) as avg_r')->first();
        $service->update([
            'total_reviews' => $stats->cnt,
            'avg_rating'    => round($stats->avg_r, 2),
        ]);
    }
}
