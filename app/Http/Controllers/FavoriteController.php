<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->active()
            ->with(['provider', 'coverImage', 'images', 'packages'])
            ->latest('favorites.created_at')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, int $service_id)
    {
        $service = Service::findOrFail($service_id);
        $user = auth()->user();
        $exists = $user->favorites()->where('service_id', $service->id)->exists();

        if ($exists) {
            $user->favorites()->detach($service->id);
            $status = 'removed';
        } else {
            $user->favorites()->attach($service->id);
            $status = 'added';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status]);
        }

        return back();
    }
}
