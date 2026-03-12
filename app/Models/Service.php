<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'provider_id', 'category_id', 'title', 'slug', 'description',
        'tags', 'status', 'avg_rating', 'total_reviews', 'total_orders', 'rejection_reason',
    ];

    protected $casts = [
        'tags'          => 'array',
        'avg_rating'    => 'decimal:2',
        'total_reviews' => 'integer',
        'total_orders'  => 'integer',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function packages()
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order');
    }

    public function coverImage()
    {
        return $this->hasOne(ServiceImage::class)->where('is_cover', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_visible', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getBasicPackage()
    {
        return $this->packages()->where('package_type', 'basic')->first();
    }

    public function getLowestPrice()
    {
        return $this->packages()->min('price');
    }
}
