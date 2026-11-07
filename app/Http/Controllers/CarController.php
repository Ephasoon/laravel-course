<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\State;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $cars = Car::query()
            ->where('user_id', auth()->id())
            ->with(['maker', 'model', 'carType', 'fuelType', 'city.state', 'primaryImage'])
            ->orderByDesc('published_at')
            ->paginate(15);

        return view('car.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(view:'car.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()
            ->route('car.index')
            ->with('success', 'Car created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        if ($car->published_at === null && auth()->id() !== $car->user_id) {
            abort(404);
        }

        $car->load([
            'maker',
            'model',
            'carType',
            'fuelType',
            'city.state',
            'user',
            'images' => fn ($query) => $query->orderBy('position'),
            'features',
        ]);

        $featureLabels = [
            'abs' => 'ABS',
            'air_conditioning' => 'Air Conditioning',
            'power_windows' => 'Power Windows',
            'power_door_locks' => 'Power Door Locks',
            'cruise_control' => 'Cruise Control',
            'bluetooth_connectivity' => 'Bluetooth Connectivity',
            'remote_start' => 'Remote Start',
            'gps_navigation' => 'GPS Navigation System',
            'heated_seats' => 'Heated Seats',
            'climate_control' => 'Climate Control',
            'rear_parking_sensors' => 'Rear Parking Sensors',
            'leather_seats' => 'Leather Seats',
        ];

        return view('car.show', compact('car', 'featureLabels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        return view(view:'car.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Car $car)
    {
        return redirect()
            ->route('car.index')
            ->with('success', 'Car updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        return redirect()
            ->route('car.index')
            ->with('success', 'Car deleted successfully.');
    }
    public function search(Request $request)
    {
        $request->validate([
            'maker_id' => ['nullable', 'integer', 'exists:makers,id'],
            'model_id' => ['nullable', 'integer', 'exists:models,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'car_type_id' => ['nullable', 'integer', 'exists:car_types,id'],
            'fuel_type_id' => ['nullable', 'integer', 'exists:fuel_types,id'],
            'year_from' => ['nullable', 'integer'],
            'year_to' => ['nullable', 'integer'],
            'price_from' => ['nullable', 'numeric'],
            'price_to' => ['nullable', 'numeric'],
            'mileage' => ['nullable', 'integer'],
            'sort' => ['nullable', 'string', 'in:price,-price,year,-year,mileage,-mileage,published_at,-published_at'],
        ]);

        $query = Car::query()
            ->whereNotNull('published_at')
            ->with(['maker', 'model', 'carType', 'fuelType', 'city.state', 'primaryImage']);

        foreach (['maker_id', 'model_id', 'car_type_id', 'fuel_type_id', 'city_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('state_id')) {
            $query->whereHas('city', function ($cityQuery) use ($request) {
                $cityQuery->where('state_id', $request->input('state_id'));
            });
        }

        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->integer('year_from'));
        }

        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->integer('year_to'));
        }

        if ($request->filled('price_from')) {
            $query->where('price', '>=', $request->input('price_from'));
        }

        if ($request->filled('price_to')) {
            $query->where('price', '<=', $request->input('price_to'));
        }

        if ($request->filled('mileage')) {
            $query->where('mileage', '<=', $request->integer('mileage'));
        }

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $query->orderBy(ltrim($sort, '-'), $direction);
        } else {
            $query->orderByDesc('published_at');
        }

        $cars = $query->paginate(15)->withQueryString();

        $makers = Maker::orderBy('name')->get();
        $models = Model::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $carTypes = CarType::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();

        return view('car.search', compact(
            'cars', 'makers', 'models', 'states', 'cities', 'carTypes', 'fuelTypes'
        ));
    }
}
