<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteCarController extends Controller
{
    public function index()
    {
        $favouriteCars = auth()->user()->favouriteCars()
            ->with(['maker', 'model', 'carType', 'fuelType', 'city', 'primaryImage'])
            ->paginate(15);

        return view('car.watchlist', compact('favouriteCars'));
    }

    public function toggle(Car $car)
    {
        if ($car->published_at === null && Auth::id() !== $car->user_id) {
            abort(404);
        }

        Auth::user()->favouriteCars()->toggle($car->id);

        return redirect()->back()
            ->with('success', auth()->user()->favouriteCars->contains($car->id)
                ? 'Added to watchlist.'
                : 'Removed from watchlist.');
    }
}