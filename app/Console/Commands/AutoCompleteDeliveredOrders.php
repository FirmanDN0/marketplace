<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class AutoCompleteDeliveredOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Auto-complete delivered orders that customer has not responded to within 3 days';

    public function handle(OrderService $orderService): int
    {
        $orders = Order::where('status', 'delivered')
            ->where('delivered_at', '<', now()->subDays(3))
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            $orderService->complete($order);
            $count++;
        }

        $this->info("Auto-completed {$count} delivered order(s).");

        return self::SUCCESS;
    }
}
