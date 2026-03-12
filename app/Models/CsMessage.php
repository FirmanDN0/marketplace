<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsMessage extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'sender_type', 'message'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(CsConversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isUser(): bool  { return $this->sender_type === 'user'; }
    public function isAi(): bool    { return $this->sender_type === 'ai'; }
    public function isAgent(): bool { return $this->sender_type === 'agent'; }
}
