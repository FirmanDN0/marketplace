<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;

use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('home_categories', 3600, function () {
            return Category::active()->roots()->with('children')->get();
        });

        $featuredServices = Cache::remember('home_featured_services', 1800, function () {
            return Service::active()
                ->with(['provider', 'coverImage', 'images', 'packages'])
                ->orderByDesc('avg_rating')
                ->limit(12)
                ->get();
        });

        return view('home', compact('categories', 'featuredServices'));
    }
}
