<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'bio', 'phone', 'country', 'city', 'website',
        'skills', 'languages', 'experience_years', 'hourly_rate',
        'balance', 'pending_balance', 'total_earned', 'total_spent',
    ];

    protected $casts = [
        'skills'    => 'array',
        'languages' => 'array',
        'balance'           => 'decimal:2',
        'pending_balance'   => 'decimal:2',
        'total_earned'      => 'decimal:2',
        'total_spent'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
