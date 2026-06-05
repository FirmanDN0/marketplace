<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\DisputeMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class DisputeController extends Controller
{
    public function show(Dispute $dispute)
    {
        $user = auth()->user();
        
        // Authorization
        if (!$user->isAdmin() && $user->id !== $dispute->order->customer_id && $user->id !== $dispute->order->provider_id) {
            abort(403);
        }

        $dispute->load(['messages.user', 'order.provider', 'order.customer', 'opener']);
        
        return view('disputes.show', compact('dispute'));
    }

    public function storeMessage(Request $request, Dispute $dispute)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $user->id !== $dispute->order->customer_id && $user->id !== $dispute->order->provider_id) {
            abort(403);
        }

        $data = $request->validate([
            'message' => 'required_without:attachment|nullable|string|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:20480',
            'type' => 'required|in:message,proof_upload,refund_proposal',
            'refund_percentage' => 'nullable|integer|min:0|max:100|required_if:type,refund_proposal'
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('dispute_proofs');
        }

        $dispute->messages()->create([
            'user_id' => $user->id,
            'message' => $data['message'] ?? null,
            'attachment_path' => $path,
            'type' => $data['type'],
            'refund_percentage' => $data['refund_percentage'] ?? null,
        ]);

        // Send notification to the other party
        if (!$user->isAdmin()) {
            $receiverId = $user->id === $dispute->order->customer_id 
                ? $dispute->order->provider_id 
                : $dispute->order->customer_id;

            NotificationService::send(
                $receiverId,
                'dispute_update',
                'Pembaruan Sengketa',
                "{$user->name} mengirim pesan/bukti baru di sengketa pesanan #{$dispute->order->order_number}.",
                ['dispute_id' => $dispute->id],
                route('disputes.show', $dispute->id)
            );
        }

        return back()->with('success', 'Pesan/Bukti berhasil diunggah.');
    }
}
