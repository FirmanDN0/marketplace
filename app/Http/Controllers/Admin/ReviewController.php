<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['customer', 'provider', 'service', 'order']);

        if ($request->filled('status')) {
            $query->where('is_visible', $request->status === 'visible');
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('provider', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        $totalReviews  = Review::count();
        $hiddenReviews = Review::where('is_visible', false)->count();

        return view('admin.reviews.index', compact('reviews', 'totalReviews', 'hiddenReviews'));
    }

    public function toggleVisibility(Review $review)
    {
        $review->update(['is_visible' => !$review->is_visible]);

        $status = $review->is_visible ? 'ditampilkan' : 'disembunyikan';
        return back()->with('success', "Review berhasil {$status}.");
    }

    public function destroy(Review $review)
    {
        $serviceId = $review->service_id;
        $review->delete();

        // Recalculate service rating
        (new \App\Services\OrderService())->updateServiceRating($serviceId);

        return back()->with('success', 'Review berhasil dihapus.');
    }
}
