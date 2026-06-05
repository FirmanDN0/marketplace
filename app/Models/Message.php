<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id', 'sender_id', 'message_text',
        'attachment_path', 'attachment_name', 'read_at',
        'custom_offer_id', 'reply_to_id'
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function customOffer()
    {
        return $this->belongsTo(CustomOffer::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
