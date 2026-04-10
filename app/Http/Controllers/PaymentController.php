<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Midtrans\Notification;

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
        $snapToken = $payment->payment_token;
        $walletBalance = auth()->user()->profile->balance ?? 0;

        return view('payment.show', compact('order', 'payment', 'snapToken', 'walletBalance'));
    }

    /**
     * Pay using wallet balance.
     */
    public function payWithWallet(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isPendingPayment()) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('info', 'Order sudah diproses.');
        }

        $walletBalance = auth()->user()->profile->balance ?? 0;

        if ($walletBalance < $order->price) {
            return back()->with('error', 'Saldo tidak mencukupi. Silakan top up terlebih dahulu.');
        }

        try {
            $this->paymentService->payWithWallet($order);
            return redirect()->route('payment.success', $order->id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Finish redirect page after Midtrans Snap payment.
     */
    public function finish(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $payment = $order->payment;

        // Re-check status from Midtrans API if still pending
        if ($payment && $payment->status === 'pending') {
            try {
                $statusObj = \Midtrans\Transaction::status($order->order_number);
                $statusArr = (array) $statusObj;
                $txStatus  = $statusArr['transaction_status'] ?? null;
                $payType   = $statusArr['payment_type'] ?? null;
                $fraud     = $statusArr['fraud_status'] ?? null;
                $txId      = $statusArr['transaction_id'] ?? null;

                if ($txStatus) {
                    $appStatus = $this->paymentService->mapMidtransStatus($txStatus, $fraud);
                    if ($appStatus === 'success') {
                        $this->paymentService->markSuccess($payment, $payType, $txId);
                    } elseif (in_array($appStatus, ['failed'])) {
                        $this->paymentService->markFailed($payment);
                    }
                }
            } catch (\Exception $e) {
                // Midtrans API error — payment may still be processed via webhook
            }

            $payment->refresh();
        }

        if ($payment && $payment->status === 'success') {
            return redirect()->route('payment.success', $order->id);
        }

        if ($payment && $payment->status === 'failed') {
            return redirect()->route('payment.failed', $order->id);
        }

        // Still pending — show pending info
        return view('payment.pending', compact('order', 'payment'));
    }

    /**
     * Midtrans server-to-server notification webhook for order payments.
     */
    public function notification(Request $request)
    {
        $payload     = $request->all();
        $serverKey   = config('services.midtrans.server_key');
        $orderId     = $payload['order_id'] ?? null;
        $statusCode  = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signature   = $payload['signature_key'] ?? null;

        // Verify Midtrans signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!$signature || !hash_equals($expectedSignature, $signature)) {
            return response('Invalid signature', 403);
        }

        $notification = new Notification();
        $txStatus     = $notification->transaction_status;
        $paymentType  = $notification->payment_type;
        $fraudStatus  = $notification->fraud_status ?? null;
        $txId         = $notification->transaction_id ?? null;

        $order = Order::where('order_number', $orderId)->first();
        if (!$order || !$order->payment) {
            return response('Not Found', 404);
        }

        $payment = $order->payment;
        $status  = $this->paymentService->mapMidtransStatus($txStatus, $fraudStatus);

        if ($status === 'success') {
            $this->paymentService->markSuccess($payment, $paymentType, $txId);
        } elseif (in_array($status, ['failed'])) {
            $this->paymentService->markFailed($payment);
        }

        return response('OK', 200);
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
