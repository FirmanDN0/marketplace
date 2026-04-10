<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'uuid',
        'title',
        'description',
        'source_url',
        'status',
        'visibility',
        'duration_seconds',
        'original_file_path',
        'thumbnail_path',
        'resolution',
        'file_size_bytes',
        'views_count',
        'downloads_count',
        'error_message',
        'metadata',
        'processed_at',
        'published_at',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'file_size_bytes' => 'integer',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the video.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get video variants (different resolutions).
     */
    public function variants()
    {
        return $this->hasMany(VideoVariant::class);
    }

    /**
     * Get video tags.
     */
    public function tags()
    {
        return $this->hasMany(VideoTag::class);
    }

    /**
     * Get processing jobs.
     */
    public function processingJobs()
    {
        return $this->hasMany(VideoProcessingJob::class);
    }

    /**
     * Get watch history entries.
     */
    public function watchHistory()
    {
        return $this->hasMany(WatchHistory::class);
    }

    /**
     * Get analytics.
     */
    public function analytics()
    {
        return $this->hasMany(VideoAnalytic::class);
    }

    /**
     * Scope for ready videos.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope for public videos.
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope for videos by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get HLS stream URL.
     */
    public function getStreamUrl(string $resolution = 'master'): string
    {
        if ($resolution === 'master') {
            return route('videos.stream', ['uuid' => $this->uuid, 'variant' => 'master']);
        }

        return route('videos.stream', ['uuid' => $this->uuid, 'variant' => $resolution]);
    }

    /**
     * Get thumbnail URL.
     */
    public function getThumbnailUrl(): string
    {
        if (!$this->thumbnail_path) {
            return asset('images/video-placeholder.jpg');
        }

        return Storage::url($this->thumbnail_path);
    }

    /**
     * Check if user can download this video.
     */
    public function canDownload(User $user): bool
    {
        return $this->user_id === $user->id || $user->role === 'admin';
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment downloads count.
     */
    public function incrementDownloads(): void
    {
        $this->increment('downloads_count');
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration_seconds) {
            return '00:00';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSize(): string
    {
        if (!$this->file_size_bytes) {
            return 'Unknown';
        }

        $size = $this->file_size_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
