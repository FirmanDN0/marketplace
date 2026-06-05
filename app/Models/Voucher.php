<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_purchase', 'max_discount',
        'quota', 'used_count', 'valid_until'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function isValidFor($amount)
    {
        if ($this->valid_until && $this->valid_until->isPast()) return false;
        if ($this->quota !== null && $this->used_count >= $this->quota) return false;
        if ($amount < $this->min_purchase) return false;
        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValidFor($amount)) return 0;
        
        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        } else {
            $discount = $amount * ($this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }
    }
}
