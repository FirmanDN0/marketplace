<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    protected $fillable = ['service_id', 'image_path', 'is_cover', 'sort_order'];

    protected $casts = [
        'is_cover'   => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
