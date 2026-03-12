<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $categories     = Category::active()->roots()->with('children')->get();
        $featuredServices = Service::active()
            ->with(['provider', 'coverImage', 'images', 'packages'])
            ->orderByDesc('avg_rating')
            ->limit(12)
            ->get();

        return view('home', compact('categories', 'featuredServices'));
    }
}
