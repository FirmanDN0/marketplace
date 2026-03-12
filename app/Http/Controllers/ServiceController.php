<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::active()->with(['provider', 'coverImage', 'images', 'packages']);

        if ($request->filled('category')) {
            $catId = (int) $request->category;
            $query->where('category_id', $catId);
        }

        // support both 'q' (new) and 'search' (legacy)
        $keyword = $request->filled('q') ? $request->q : ($request->filled('search') ? $request->search : null);
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->whereHas('packages', fn($q) => $q->where('price', '>=', (float) $request->min_price));
        }

        if ($request->filled('max_price')) {
            $query->whereHas('packages', fn($q) => $q->where('price', '<=', (float) $request->max_price));
        }

        if ($request->filled('min_rating')) {
            $query->where('avg_rating', '>=', (float) $request->min_rating);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc'  => $query->join('service_packages', 'services.id', '=', 'service_packages.service_id')
                                      ->orderBy('service_packages.price')->select('services.*')->distinct(),
                'price_desc' => $query->join('service_packages', 'services.id', '=', 'service_packages.service_id')
                                      ->orderByDesc('service_packages.price')->select('services.*')->distinct(),
                'rating'     => $query->orderByDesc('avg_rating'),
                'orders'     => $query->orderByDesc('total_orders'),
                default      => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $services   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->with('children')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        if ($service->status !== 'active') {
            abort(404);
        }

        $service->load(['provider.profile', 'packages', 'images', 'reviews.customer', 'category']);

        $relatedServices = Service::active()
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->limit(4)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }
}
