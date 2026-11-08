<?php

namespace App\Http\Controllers;

use App\Actions\SaveCar;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use App\Support\Catalog;
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
            ->withTrashed()
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
        return view('car.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request, SaveCar $saveCar)
    {
        $saveCar->store($request, $request->user());

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

        $favouriteIds = auth()->check() ? auth()->user()->favouriteCars->pluck('id')->toArray() : [];

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

        return view('car.show', compact('car', 'featureLabels', 'favouriteIds'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        $this->authorize('update', $car);

        $car->load([
            'features',
            'city',
            'images' => fn ($query) => $query->orderBy('position'),
        ]);

        return view('car.edit', array_merge($this->formData(), compact('car')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car, SaveCar $saveCar)
    {
        $this->authorize('update', $car);

        $saveCar->update($request, $car);

        return redirect()
            ->route('car.index')
            ->with('success', 'Car updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $this->authorize('delete', $car);

        $car->delete();

        return redirect()
            ->route('car.index')
            ->with('success', 'Car deleted successfully.');
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(Car $car)
    {
        $this->authorize('restore', $car);

        $car->restore();

        return redirect()
            ->route('car.index')
            ->with('success', 'Car restored successfully.');
    }

    public function search(Request $request)
    {
        $filters = $request->validate([
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
            ->with(['maker', 'model', 'carType', 'fuelType', 'city.state', 'primaryImage'])
            ->filter($filters);

        $cars = $query->paginate(15)->withQueryString();

        $favouriteIds = auth()->check() ? auth()->user()->favouriteCars->pluck('id')->toArray() : [];

        $makers = Catalog::makers();
        $models = Catalog::models();
        $states = Catalog::states();
        $cities = Catalog::cities();
        $carTypes = Catalog::carTypes();
        $fuelTypes = Catalog::fuelTypes();

        return view('car.search', compact(
            'cars', 'makers', 'models', 'states', 'cities', 'carTypes', 'fuelTypes', 'favouriteIds'
        ));
    }

    private function formData(): array
    {
        return [
            'makers' => Catalog::makers(),
            'models' => Catalog::models(),
            'carTypes' => Catalog::carTypes(),
            'fuelTypes' => Catalog::fuelTypes(),
            'states' => Catalog::states(),
            'cities' => Catalog::cities(),
        ];
    }
}
