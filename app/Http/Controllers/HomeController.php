<?php

namespace App\Http\Controllers;

use App\Models\Car;

class HomeController extends Controller
{
    public function index()
    {
        $cars = Car::query()
            ->whereNotNull('published_at')
            ->with(['maker', 'model', 'carType', 'fuelType', 'city.state', 'primaryImage'])
            ->orderByDesc('published_at')
            ->limit(12)
            ->get();

        $favouriteIds = auth()->check() ? auth()->user()->favouriteCars->pluck('id')->toArray() : [];

        return view('index', compact('cars', 'favouriteIds'));
    }
}
