<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\OrderService;

class AutoCompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically complete orders that have been delivered for more than 3 days without customer action.';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService)
    {
        $cutoffDate = now()->subDays(3);

        $orders = Order::where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $cutoffDate)
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            try {
                $orderService->complete($order);
                
                \App\Services\NotificationService::send(
                    $order->customer_id,
                    'order_completed',
                    'Order Auto-Completed',
                    "Order #{$order->order_number} has been automatically marked as completed since 3 days have passed after delivery.",
                    ['order_id' => $order->id],
                    route('customer.orders.show', $order->id)
                );

                $count++;
            } catch (\Exception $e) {
                \Log::error("Failed to auto-complete order {$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully auto-completed {$count} orders.");
    }
}
