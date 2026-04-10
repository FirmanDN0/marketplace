<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'resolution',
        'format',
        'file_path',
        'playlist_path',
        'file_size_bytes',
        'bitrate_kbps',
        'codec',
        'status',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'bitrate_kbps' => 'integer',
    ];

    /**
     * Get the video.
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope for ready variants.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope for HLS format.
     */
    public function scopeHls($query)
    {
        return $query->where('format', 'hls');
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
