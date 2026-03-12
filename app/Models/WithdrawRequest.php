<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    protected $fillable = [
        'provider_id', 'amount', 'method', 'account_details',
        'status', 'notes', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'account_details' => 'array',
        'processed_at'    => 'datetime',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
