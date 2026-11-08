<?php

namespace App\Actions;

use App\Models\Car;
use App\Models\CarFeatures;
use App\Models\CarImages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaveCar
{
    private const FEATURE_COLUMNS = [
        'abs',
        'air_conditioning',
        'power_windows',
        'power_door_locks',
        'cruise_control',
        'bluetooth_connectivity',
        'remote_start',
        'gps_navigation',
        'heated_seats',
        'climate_control',
        'rear_parking_sensors',
        'leather_seats',
    ];

    public function store(Request $request, User $user): Car
    {
        return DB::transaction(function () use ($request, $user) {
            $car = Car::create([
                'maker_id' => $request->integer('maker_id'),
                'model_id' => $request->integer('model_id'),
                'year' => $request->integer('year'),
                'car_type_id' => $request->integer('car_type_id'),
                'fuel_type_id' => $request->integer('fuel_type_id'),
                'city_id' => $request->integer('city_id'),
                'price' => $request->integer('price'),
                'mileage' => $request->integer('mileage'),
                'vin' => $request->string('vin')->toString(),
                'phone' => $request->string('phone')->toString(),
                'address' => $request->input('address'),
                'description' => $request->input('description'),
                'user_id' => $user->id,
                'published_at' => $request->boolean('published') ? now() : null,
            ]);

            CarFeatures::create([
                'car_id' => $car->id,
                ...$this->featureValues($request),
            ]);

            foreach ($this->uploadedImages($request) as $position => $file) {
                $path = $file->store('cars/'.$car->id, 'public');

                CarImages::create([
                    'car_id' => $car->id,
                    'image_path' => Storage::url($path),
                    'position' => $position + 1,
                ]);
            }

            return $car;
        });
    }

    public function update(Request $request, Car $car): Car
    {
        return DB::transaction(function () use ($request, $car) {
            $car->update([
                'maker_id' => $request->integer('maker_id'),
                'model_id' => $request->integer('model_id'),
                'year' => $request->integer('year'),
                'car_type_id' => $request->integer('car_type_id'),
                'fuel_type_id' => $request->integer('fuel_type_id'),
                'city_id' => $request->integer('city_id'),
                'price' => $request->integer('price'),
                'mileage' => $request->integer('mileage'),
                'vin' => $request->string('vin')->toString(),
                'phone' => $request->string('phone')->toString(),
                'address' => $request->input('address'),
                'description' => $request->input('description'),
                'published_at' => $request->boolean('published')
                    ? ($car->published_at ?? now())
                    : null,
            ]);

            CarFeatures::updateOrCreate(
                ['car_id' => $car->id],
                ['car_id' => $car->id, ...$this->featureValues($request)]
            );

            $deleteIds = $request->input('delete_images', []);

            if (! empty($deleteIds)) {
                $images = $car->images()->whereIn('id', $deleteIds)->get();

                foreach ($images as $image) {
                    if (str_starts_with($image->image_path, '/storage/')) {
                        Storage::disk('public')->delete(Str::after($image->image_path, '/storage/'));
                    }

                    $image->delete();
                }
            }

            $position = (int) $car->images()->max('position');

            foreach ($this->uploadedImages($request) as $file) {
                $position++;
                $path = $file->store('cars/'.$car->id, 'public');

                CarImages::create([
                    'car_id' => $car->id,
                    'image_path' => Storage::url($path),
                    'position' => $position,
                ]);
            }

            $car->images()->orderBy('position')->get()->each(function (CarImages $image, int $index) {
                $image->update(['position' => $index + 1]);
            });

            return $car;
        });
    }

    private function featureValues(Request $request): array
    {
        $values = [];

        foreach (self::FEATURE_COLUMNS as $column) {
            $values[$column] = $request->boolean($column);
        }

        return $values;
    }

    private function uploadedImages(Request $request): array
    {
        $files = $request->file('images', []);

        return is_array($files) ? $files : [$files];
    }
}
