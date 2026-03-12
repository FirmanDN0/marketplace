<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function show(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isPendingPayment()) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('info', 'This order has already been processed.');
        }

        $payment = $order->payment ?? $this->paymentService->initiate($order);

        return view('payment.show', compact('order', 'payment'));
    }

    public function process(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'action' => 'required|in:pay,cancel',
        ]);

        $payment = $order->payment;

        if (!$payment) {
            return back()->withErrors(['error' => 'Payment record not found.']);
        }

        if ($data['action'] === 'pay') {
            $this->paymentService->markSuccess($payment);
            return redirect()->route('payment.success', $order->id);
        }

        $this->paymentService->markFailed($payment);
        return redirect()->route('payment.failed', $order->id);
    }

    public function success(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.success', compact('order'));
    }

    public function failed(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.failed', compact('order'));
    }
}
