<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProcessingJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'job_type',
        'status',
        'progress_percent',
        'output_data',
        'error_message',
        'started_at',
        'completed_at',
        'attempts',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'output_data' => 'array',
        'attempts' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the video.
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope for pending jobs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for processing jobs.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for completed jobs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed jobs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark as started.
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
            'attempts' => $this->attempts + 1,
        ]);
    }

    /**
     * Mark as completed.
     */
    public function markAsCompleted(array $outputData = []): void
    {
        $this->update([
            'status' => 'completed',
            'progress_percent' => 100,
            'output_data' => $outputData,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Update progress.
     */
    public function updateProgress(int $percent): void
    {
        $this->update(['progress_percent' => min(100, max(0, $percent))]);
    }
}
