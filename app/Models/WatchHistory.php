<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_history';

    protected $fillable = [
        'user_id',
        'video_id',
        'watched_duration_seconds',
        'completed',
        'last_position_seconds',
        'quality',
    ];

    protected $casts = [
        'watched_duration_seconds' => 'integer',
        'completed' => 'boolean',
        'last_position_seconds' => 'integer',
    ];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video.
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope for completed watches.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Mark as completed if watched > 90%.
     */
    public function checkAndMarkCompleted(int $videoDuration): void
    {
        if ($this->watched_duration_seconds >= ($videoDuration * 0.9)) {
            $this->update(['completed' => true]);
        }
    }

    /**
     * Update watch position.
     */
    public function updatePosition(int $position, int $duration): void
    {
        $this->update([
            'last_position_seconds' => $position,
            'watched_duration_seconds' => max($this->watched_duration_seconds, $position),
        ]);

        $this->checkAndMarkCompleted($duration);
    }
}
