<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'status', 'avatar', 'email_verified_at',
        'provider_setup_step',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ------------------------------------------------------------------
    // Role helpers
    // ------------------------------------------------------------------
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isProvider(): bool { return $this->role === 'provider'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }
    public function isActive(): bool   { return $this->status === 'active'; }
    public function hasCompletedOnboarding(): bool { return !$this->isProvider() || $this->provider_setup_step >= 3; }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsProvider()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'provider_id');
    }

    public function conversationsAsCustomer()
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    public function conversationsAsProvider()
    {
        return $this->hasMany(Conversation::class, 'provider_id');
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class, 'user_id');
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'provider_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Service::class, 'favorites')->withTimestamps();
    }

    public function hasFavorited(int $serviceId): bool
    {
        return $this->favorites()->where('service_id', $serviceId)->exists();
    }
}

