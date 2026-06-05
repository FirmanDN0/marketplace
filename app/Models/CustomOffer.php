<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOffer extends Model
{
    protected $fillable = [
        'conversation_id', 'provider_id', 'customer_id', 'service_id',
        'title', 'description', 'price', 'delivery_days', 'status'
    ];

    public function conversation() { return $this->belongsTo(Conversation::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function service() { return $this->belongsTo(Service::class); }
    public function order() { return $this->hasOne(Order::class); }
}
