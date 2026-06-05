<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderPortfolio extends Model
{
    protected $fillable = [
        'provider_id', 'title', 'description', 'media_path', 'media_type', 'sort_order'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
