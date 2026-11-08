<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $ids = Cache::remember('home.latest_car_ids', 300, function () {
            return Car::query()
                ->whereNotNull('published_at')
                ->orderByDesc('published_at')
                ->limit(12)
                ->pluck('id')
                ->all();
        });

        $cars = Car::query()
            ->whereIn('id', $ids)
            ->with(['maker', 'model', 'carType', 'fuelType', 'city.state', 'primaryImage'])
            ->get()
            ->sortBy(fn ($c) => array_search($c->id, $ids))
            ->values();

        $favouriteIds = auth()->check() ? auth()->user()->favouriteCars->pluck('id')->toArray() : [];

        return view('index', compact('cars', 'favouriteIds'));
    }
}
