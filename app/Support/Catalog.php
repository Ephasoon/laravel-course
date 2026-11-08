<?php

namespace App\Support;

use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\State;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class Catalog
{
    private const TTL = 3600;

    private const KEYS = [
        'catalog.makers',
        'catalog.models',
        'catalog.states',
        'catalog.cities',
        'catalog.car_types',
        'catalog.fuel_types',
    ];

    public static function makers(): Collection
    {
        return Maker::hydrate(Cache::remember(
            'catalog.makers',
            self::TTL,
            fn () => Maker::orderBy('name')->get()->toArray()
        ));
    }

    public static function models(): Collection
    {
        return Model::hydrate(Cache::remember(
            'catalog.models',
            self::TTL,
            fn () => Model::orderBy('name')->get()->toArray()
        ));
    }

    public static function states(): Collection
    {
        return State::hydrate(Cache::remember(
            'catalog.states',
            self::TTL,
            fn () => State::orderBy('name')->get()->toArray()
        ));
    }

    public static function cities(): Collection
    {
        return City::hydrate(Cache::remember(
            'catalog.cities',
            self::TTL,
            fn () => City::orderBy('name')->get()->toArray()
        ));
    }

    public static function carTypes(): Collection
    {
        return CarType::hydrate(Cache::remember(
            'catalog.car_types',
            self::TTL,
            fn () => CarType::orderBy('name')->get()->toArray()
        ));
    }

    public static function fuelTypes(): Collection
    {
        return FuelType::hydrate(Cache::remember(
            'catalog.fuel_types',
            self::TTL,
            fn () => FuelType::orderBy('name')->get()->toArray()
        ));
    }

    public static function forget(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }
}
