<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use App\Models\Order;
use App\Models\DisputeMessage;

class AttachmentController extends Controller
{
    public function downloadMessageAttachment(Message $message)
    {
        // Validasi kepemilikan percakapan atau akses admin
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak: Anda bukan bagian dari percakapan ini.');
        }

        if (!$message->attachment_path) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        // Cek di private storage (local)
        if (Storage::disk('local')->exists($message->attachment_path)) {
            return Storage::disk('local')->download($message->attachment_path);
        }

        // Fallback: Jika file tersebut di-upload sebelum update ini (di public storage)
        if (Storage::disk('public')->exists($message->attachment_path)) {
            return Storage::disk('public')->download($message->attachment_path);
        }

        abort(404, 'File lampiran hilang atau telah dihapus.');
    }

    public function downloadDeliveryFile(Order $order)
    {
        // Validasi partisipasi pesanan
        if ($order->customer_id !== auth()->id() && $order->provider_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak atas pesanan ini.');
        }

        if (!$order->delivery_file) {
            abort(404, 'File hasil kerja tidak ditemukan.');
        }

        // Cek di private storage (local)
        if (Storage::disk('local')->exists($order->delivery_file)) {
            return Storage::disk('local')->download($order->delivery_file);
        }

        // Fallback: Jika file tersebut di-upload sebelum update ini (di public storage)
        if (Storage::disk('public')->exists($order->delivery_file)) {
            return Storage::disk('public')->download($order->delivery_file);
        }

        abort(404, 'File hasil kerja hilang atau telah dihapus.');
    }

    public function downloadRequirementsFile(Order $order)
    {
        // Validasi partisipasi pesanan
        if ($order->customer_id !== auth()->id() && $order->provider_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak atas pesanan ini.');
        }

        if (!$order->requirements_file) {
            abort(404, 'File persyaratan tidak ditemukan.');
        }

        // Cek di private storage (local)
        if (Storage::disk('local')->exists($order->requirements_file)) {
            return Storage::disk('local')->download($order->requirements_file);
        }

        // Fallback: Jika file tersebut di-upload sebelum update ini (di public storage)
        if (Storage::disk('public')->exists($order->requirements_file)) {
            return Storage::disk('public')->download($order->requirements_file);
        }

        abort(404, 'File persyaratan hilang atau telah dihapus.');
    }

    public function downloadDisputeAttachment(DisputeMessage $message)
    {
        $order = $message->dispute->order;

        // Validasi partisipasi
        if ($order->customer_id !== auth()->id() && $order->provider_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if (!$message->attachment_path) {
            abort(404, 'File bukti tidak ditemukan.');
        }

        if (Storage::disk('local')->exists($message->attachment_path)) {
            return Storage::disk('local')->download($message->attachment_path);
        }

        if (Storage::disk('public')->exists($message->attachment_path)) {
            return Storage::disk('public')->download($message->attachment_path);
        }

        abort(404, 'File bukti hilang.');
    }
}
