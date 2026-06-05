<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderInvoiceController extends Controller
{
    public function download(Order $order)
    {
        $user = auth()->user();

        // Validasi: Hanya customer, provider terkait, atau admin yang bisa unduh invoice
        if ($user->id !== $order->customer_id && $user->id !== $order->provider_id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak untuk mengunduh invoice ini.');
        }

        // Invoice hanya tersedia jika pesanan sudah dibayar (minimal In Progress)
        if ($order->status === 'pending_payment' || $order->status === 'cancelled') {
            abort(404, 'Invoice belum tersedia untuk pesanan ini.');
        }

        $order->load(['customer.profile', 'provider.profile', 'service', 'payment']);

        $pdf = Pdf::loadView('orders.invoice', compact('order'));
        
        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Invoice_{$order->order_number}.pdf");
    }
}
