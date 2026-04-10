<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class AutoCancelUnpaidOrders extends Command
{
    protected $signature = 'orders:auto-cancel';
    protected $description = 'Cancel orders that have not been paid within 24 hours';

    public function handle(): int
    {
        $orders = Order::where('status', 'pending_payment')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            $order->update([
                'status'           => 'cancelled',
                'cancelled_reason' => 'Otomatis dibatalkan: tidak dibayar dalam 24 jam.',
                'cancelled_at'     => now(),
            ]);

            NotificationService::send(
                $order->customer_id,
                'order_cancelled',
                'Order Dibatalkan',
                "Order #{$order->order_number} dibatalkan otomatis karena tidak dibayar dalam 24 jam.",
                ['order_id' => $order->id]
            );

            NotificationService::send(
                $order->provider_id,
                'order_cancelled',
                'Order Dibatalkan',
                "Order #{$order->order_number} dibatalkan otomatis karena customer tidak membayar.",
                ['order_id' => $order->id]
            );

            $count++;
        }

        $this->info("Auto-cancelled {$count} unpaid order(s).");

        return self::SUCCESS;
    }
}
