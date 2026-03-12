<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $fillable = [
        'service_id', 'package_type', 'name', 'description',
        'price', 'delivery_days', 'revisions', 'features', 'is_active',
    ];

    protected $casts = [
        'features'      => 'array',
        'price'         => 'decimal:2',
        'delivery_days' => 'integer',
        'revisions'     => 'integer',
        'is_active'     => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'package_id');
    }

    public function hasUnlimitedRevisions(): bool
    {
        return $this->revisions === -1;
    }
}
