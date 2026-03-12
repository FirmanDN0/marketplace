<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function reply(Request $request, Review $review)
    {
        // Only the provider who owns this review may reply
        if ($review->provider_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'provider_reply' => 'required|string|min:5|max:1000',
        ]);

        $review->update([
            'provider_reply' => $data['provider_reply'],
            'replied_at'     => now(),
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }

    public function deleteReply(Review $review)
    {
        if ($review->provider_id !== auth()->id()) {
            abort(403);
        }

        $review->update([
            'provider_reply' => null,
            'replied_at'     => null,
        ]);

        return back()->with('success', 'Reply removed.');
    }
}
