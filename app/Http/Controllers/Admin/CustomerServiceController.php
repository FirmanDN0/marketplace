<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CsConversation;
use App\Models\CsMessage;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'human');

        $query = CsConversation::with(['user', 'lastMessage', 'agent'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $conversations = $query->paginate(20);

        $counts = [
            'all'    => CsConversation::count(),
            'ai'     => CsConversation::where('status', 'ai')->count(),
            'human'  => CsConversation::where('status', 'human')->count(),
            'closed' => CsConversation::where('status', 'closed')->count(),
        ];

        return view('admin.customer-service.index', compact('conversations', 'status', 'counts'));
    }

    public function show(CsConversation $conversation)
    {
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        return view('admin.customer-service.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, CsConversation $conversation)
    {
        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $agent = auth()->user();

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $agent->id,
            'sender_type'     => 'agent',
            'message'         => $data['message'],
        ]);

        // Assign agent and ensure status is human
        $updates = [];
        if ($conversation->status !== 'human') {
            $updates['status'] = 'human';
        }
        if (!$conversation->agent_id) {
            $updates['agent_id'] = $agent->id;
        }
        if ($updates) {
            $conversation->update($updates);
        }

        return redirect()->route('admin.customer-service.show', $conversation->id);
    }

    public function close(CsConversation $conversation)
    {
        $conversation->update(['status' => 'closed']);

        CsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => auth()->id(),
            'sender_type'     => 'agent',
            'message'         => 'Percakapan ini telah ditutup oleh agen. Terima kasih telah menghubungi Customer Service kami. Semoga masalah Anda telah terselesaikan! 😊',
        ]);

        return redirect()->route('admin.customer-service.index')
            ->with('success', 'Percakapan berhasil ditutup.');
    }

    public function reopen(CsConversation $conversation)
    {
        $conversation->update(['status' => 'human']);

        return redirect()->route('admin.customer-service.show', $conversation->id)
            ->with('success', 'Percakapan dibuka kembali.');
    }
}
