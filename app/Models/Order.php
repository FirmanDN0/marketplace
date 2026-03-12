<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'provider_id', 'service_id', 'package_id',
        'price', 'platform_fee', 'provider_earning', 'status',
        'delivery_deadline', 'notes', 'delivery_file', 'delivery_message',
        'delivered_at', 'completed_at', 'cancelled_at', 'cancelled_by', 'cancelled_reason',
        'revision_count', 'revision_message', 'revision_requested_at',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'platform_fee'      => 'decimal:2',
        'provider_earning'  => 'decimal:2',
        'delivery_deadline' => 'datetime',
        'delivered_at'           => 'datetime',
        'completed_at'           => 'datetime',
        'cancelled_at'           => 'datetime',
        'revision_requested_at'  => 'datetime',
        'revision_count'         => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // ------------------------------------------------------------------
    // Status helpers
    // ------------------------------------------------------------------
    public function isPendingPayment(): bool { return $this->status === 'pending_payment'; }
    public function isPaid(): bool           { return $this->status === 'paid'; }
    public function isInProgress(): bool     { return $this->status === 'in_progress'; }
    public function isDelivered(): bool      { return $this->status === 'delivered'; }
    public function isCompleted(): bool      { return $this->status === 'completed'; }
    public function isCancelled(): bool      { return $this->status === 'cancelled'; }
    public function isDisputed(): bool       { return $this->status === 'disputed'; }

    public function canBeReviewed(): bool
    {
        return $this->isCompleted() && $this->review === null;
    }

    public function canRequestRevision(): bool
    {
        if (!$this->isDelivered()) {
            return false;
        }

        $package = $this->package;
        if (!$package) {
            return false;
        }

        // -1 means unlimited revisions
        if ($package->revisions === -1) {
            return true;
        }

        return $this->revision_count < $package->revisions;
    }

    public function revisionsRemaining(): int|string
    {
        $package = $this->package;
        if (!$package) {
            return 0;
        }

        if ($package->revisions === -1) {
            return '∞';
        }

        return max(0, $package->revisions - $this->revision_count);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}
