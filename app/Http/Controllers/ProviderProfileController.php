<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;

class ProviderProfileController extends Controller
{
    public function show(string $username)
    {
        $provider = User::where('username', $username)
            ->where('role', 'provider')
            ->where('status', 'active')
            ->with('profile')
            ->firstOrFail();

        $services = $provider->services()
            ->where('status', 'active')
            ->withCount('reviews')
            ->with(['coverImage', 'packages', 'category'])
            ->latest()
            ->get();

        $reviews = Review::where('provider_id', $provider->id)
            ->where('is_visible', true)
            ->with(['customer', 'service'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_services' => $services->count(),
            'total_orders'   => $provider->ordersAsProvider()->whereIn('status', ['completed'])->count(),
            'avg_rating'     => $reviews->avg('rating') ?? 0,
            'total_reviews'  => Review::where('provider_id', $provider->id)->where('is_visible', true)->count(),
            'member_since'   => $provider->created_at,
        ];

        return view('provider-profile.show', compact('provider', 'services', 'reviews', 'stats'));
    }
}
