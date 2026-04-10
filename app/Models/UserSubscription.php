<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'trial_ends_at',
        'payment_id',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the payment associated with subscription.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get subscription payments.
     */
    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_id');
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('current_period_end', '>', now());
    }

    /**
     * Scope for expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('current_period_end', '<=', now());
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->current_period_end->isFuture();
    }

    /**
     * Check if subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' || !is_null($this->cancelled_at);
    }

    /**
     * Get days remaining.
     */
    public function daysRemaining(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return now()->diffInDays($this->current_period_end, false);
    }

    /**
     * Check if can upgrade to a plan.
     */
    public function canUpgrade(SubscriptionPlan $newPlan): bool
    {
        return $newPlan->price > $this->plan->price;
    }
}
