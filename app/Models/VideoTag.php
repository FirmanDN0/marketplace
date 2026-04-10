<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'tag',
        'confidence_score',
        'source',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
    ];

    /**
     * Get the video.
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope for AI tags.
     */
    public function scopeAi($query)
    {
        return $query->where('source', 'ai');
    }

    /**
     * Scope for manual tags.
     */
    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }
}
