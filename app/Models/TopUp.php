<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'amount', 'status',
        'snap_token', 'payment_type', 'gateway_response', 'paid_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isSuccess(): bool  { return $this->status === 'success'; }
    public function isFailed(): bool   { return in_array($this->status, ['failed', 'expired']); }
}
