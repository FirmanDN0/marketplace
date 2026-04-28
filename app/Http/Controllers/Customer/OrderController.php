<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService  $orderService,
        private PaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $query = Order::where('customer_id', auth()->id())
            ->with(['service', 'provider', 'package']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['service', 'provider.profile', 'package', 'payment', 'review', 'dispute']);
        return view('customer.orders.show', compact('order'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
        ]);

        $package = ServicePackage::with('service')->findOrFail($request->package_id);
        $service = $package->service;

        if ($service->status !== 'active') {
            abort(404);
        }

        return view('customer.orders.create', compact('service', 'package'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'notes'      => 'nullable|string|max:1000',
        ]);

        $package = ServicePackage::with('service')->findOrFail($data['package_id']);
        $service = $package->service;

        if ($service->status !== 'active') {
            abort(404);
        }

        $order   = $this->orderService->create(auth()->id(), $service, $package, $data['notes'] ?? null);
        $payment = $this->paymentService->initiate($order);

        return redirect()->route('payment.show', $order->id);
    }

    public function submitRequirements(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isWaitingRequirements()) {
            return back()->withErrors(['error' => 'Requirements cannot be submitted for this order.']);
        }

        $data = $request->validate([
            'requirements'      => 'required|string|min:10|max:3000',
            'requirements_file' => 'nullable|file|mimes:zip,rar,pdf,doc,docx,jpg,png,fig,ai,psd|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('requirements_file')) {
            $filePath = $request->file('requirements_file')->store('requirements', 'public');
        }

        $deadline = now()->addDays($order->package->delivery_days);

        $order->update([
            'requirements'              => $data['requirements'],
            'requirements_file'         => $filePath,
            'requirements_submitted_at' => now(),
            'status'                    => 'in_progress',
            'delivery_deadline'         => $deadline,
        ]);

        \App\Services\NotificationService::send(
            $order->provider_id,
            'order_in_progress',
            'Requirements Submitted',
            "Customer has submitted requirements for order #{$order->order_number}. The delivery timer has started.",
            ['order_id' => $order->id],
            route('provider.orders.show', $order->id)
        );

        return back()->with('success', 'Requirements submitted successfully! The provider has started working on your order.');
    }

    public function accept(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isDelivered()) {
            return back()->withErrors(['error' => 'Order must be in delivered state to accept.']);
        }

        $this->orderService->complete($order);

        return back()->with('success', 'Order accepted and completed!');
    }

    public function requestRevision(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canRequestRevision()) {
            return back()->withErrors(['error' => 'Revision cannot be requested for this order.']);
        }

        $data = $request->validate([
            'revision_message' => 'required|string|min:20|max:1000',
        ]);

        $this->orderService->requestRevision($order, $data['revision_message']);

        return back()->with('success', 'Revision requested! The provider will rework and resubmit.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending_payment', 'paid'])) {
            return back()->withErrors(['error' => 'This order cannot be cancelled.']);
        }

        $data = $request->validate(['reason' => 'required|string|max:500']);

        $this->orderService->cancel($order, auth()->id(), $data['reason']);

        return back()->with('success', 'Order cancelled.');
    }

    public function dispute(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->isDelivered()) {
            return back()->withErrors(['error' => 'Only delivered orders can be disputed.']);
        }

        $data = $request->validate([
            'reason'      => 'required|string|max:200',
            'description' => 'required|string|min:30|max:2000',
        ]);

        Dispute::create([
            'order_id'    => $order->id,
            'opened_by'   => auth()->id(),
            'reason'      => $data['reason'],
            'description' => $data['description'],
        ]);

        $order->update(['status' => 'disputed']);

        \App\Services\NotificationService::send(
            $order->provider_id,
            'order_disputed',
            'Order Disputed',
            "Customer has opened a dispute for order #{$order->order_number}.",
            ['order_id' => $order->id]
        );

        return back()->with('success', 'Dispute submitted. Admin will review it.');
    }
}
