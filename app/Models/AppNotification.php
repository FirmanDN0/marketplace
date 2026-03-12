<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// We name the class AppNotification to avoid conflict with Laravel's built-in Notification class
class AppNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'data', 'action_url', 'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
