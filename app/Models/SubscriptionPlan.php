<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'interval',
        'max_resolution',
        'max_uploads_per_month',
        'max_storage_gb',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_uploads_per_month' => 'integer',
        'max_storage_gb' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get subscriptions for this plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if unlimited uploads.
     */
    public function hasUnlimitedUploads(): bool
    {
        return is_null($this->max_uploads_per_month);
    }

    /**
     * Check if unlimited storage.
     */
    public function hasUnlimitedStorage(): bool
    {
        return is_null($this->max_storage_gb);
    }
}
