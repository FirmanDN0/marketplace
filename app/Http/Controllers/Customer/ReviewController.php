<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Services\OrderService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function create(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeReviewed()) {
            return redirect()->route('customer.orders.show', $order->id)
                ->withErrors(['error' => 'This order cannot be reviewed.']);
        }

        return view('customer.reviews.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeReviewed()) {
            abort(422, 'Order cannot be reviewed.');
        }

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        Review::create([
            'order_id'    => $order->id,
            'customer_id' => auth()->id(),
            'provider_id' => $order->provider_id,
            'service_id'  => $order->service_id,
            'rating'      => $data['rating'],
            'comment'     => $data['comment'],
        ]);

        $this->orderService->updateServiceRating($order->service_id);

        \App\Services\NotificationService::send(
            $order->provider_id,
            'new_review',
            'New Review',
            "You received a {$data['rating']}-star review for order #{$order->order_number}.",
            ['order_id' => $order->id],
            route('provider.orders.show', $order->id)
        );

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Review submitted!');
    }
}
