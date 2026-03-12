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
    public function create(int $customerId, Service $service, ServicePackage $package, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($customerId, $service, $package, $notes) {
            $deadline = now()->addDays($package->delivery_days);

            $order = Order::create([
                'customer_id'     => $customerId,
                'provider_id'     => $service->provider_id,
                'service_id'      => $service->id,
                'package_id'      => $package->id,
                'price'           => $package->price,
                'status'          => 'pending_payment',
                'delivery_deadline' => $deadline,
                'notes'           => $notes,
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

            // Credit provider balance
            $profile = UserProfile::firstOrCreate(['user_id' => $order->provider_id]);
            $balanceBefore = $profile->balance;
            $profile->increment('balance', $order->provider_earning);
            $profile->increment('total_earned', $order->provider_earning);

            Transaction::create([
                'user_id'        => $order->provider_id,
                'payment_id'     => optional($order->payment)->id,
                'type'           => 'earning',
                'amount'         => $order->provider_earning,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $order->provider_earning,
                'description'    => "Earning for Order #{$order->order_number}",
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
     * Cancel an order.
     */
    public function cancel(Order $order, int $cancelledBy, string $reason): void
    {
        $order->update([
            'status'          => 'cancelled',
            'cancelled_by'    => $cancelledBy,
            'cancelled_reason' => $reason,
            'cancelled_at'    => now(),
        ]);

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
