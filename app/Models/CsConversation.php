<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CsConversation extends Model
{
    protected $fillable = ['user_id', 'agent_id', 'subject', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CsMessage::class, 'conversation_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(CsMessage::class, 'conversation_id')->latestOfMany();
    }

    public function isAi(): bool     { return $this->status === 'ai'; }
    public function isHuman(): bool  { return $this->status === 'human'; }
    public function isClosed(): bool { return $this->status === 'closed'; }
}
