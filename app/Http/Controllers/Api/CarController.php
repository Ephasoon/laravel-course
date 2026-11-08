<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CarController extends Controller
{
    /**
     * @return array<int|string, mixed>
     */
    private function eagerLoad(): array
    {
        return [
            'maker',
            'model',
            'carType',
            'fuelType',
            'city.state',
            'primaryImage',
            'user',
            'images' => fn ($query) => $query->orderBy('position'),
            'features',
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function filterRules(): array
    {
        return [
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
        ];
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate($this->filterRules());

        $cars = Car::query()
            ->whereNotNull('published_at')
            ->with($this->eagerLoad())
            ->filter($filters)
            ->paginate(15)
            ->withQueryString();

        return CarResource::collection($cars);
    }

    public function show(Car $car): CarResource
    {
        if ($car->published_at === null) {
            abort(404);
        }

        $car->load($this->eagerLoad());

        return new CarResource($car);
    }

    public function myCars(Request $request): AnonymousResourceCollection
    {
        $cars = Car::query()
            ->where('user_id', $request->user()->id)
            ->with($this->eagerLoad())
            ->orderByDesc('published_at')
            ->paginate(15)
            ->withQueryString();

        return CarResource::collection($cars);
    }
}
