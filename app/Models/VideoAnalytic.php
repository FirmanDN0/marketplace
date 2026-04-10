<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'date',
        'views',
        'unique_viewers',
        'watch_time_minutes',
        'avg_view_duration_seconds',
        'completion_rate',
        'downloads',
    ];

    protected $casts = [
        'date' => 'date',
        'views' => 'integer',
        'unique_viewers' => 'integer',
        'watch_time_minutes' => 'integer',
        'avg_view_duration_seconds' => 'integer',
        'completion_rate' => 'decimal:2',
        'downloads' => 'integer',
    ];

    /**
     * Get the video.
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Increment views.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Increment unique viewers.
     */
    public function incrementUniqueViewers(): void
    {
        $this->increment('unique_viewers');
    }

    /**
     * Add watch time.
     */
    public function addWatchTime(int $minutes): void
    {
        $this->increment('watch_time_minutes', $minutes);
    }

    /**
     * Increment downloads.
     */
    public function incrementDownloads(): void
    {
        $this->increment('downloads');
    }

    /**
     * Get or create today's analytics.
     */
    public static function getTodayFor(int $videoId): self
    {
        return self::firstOrCreate(
            [
                'video_id' => $videoId,
                'date' => now()->toDateString(),
            ],
            [
                'views' => 0,
                'unique_viewers' => 0,
                'watch_time_minutes' => 0,
                'avg_view_duration_seconds' => 0,
                'completion_rate' => 0.00,
                'downloads' => 0,
            ]
        );
    }
}
