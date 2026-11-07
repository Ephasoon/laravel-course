<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'maker_id',
    'model_id',
    'year',
    'price',
    'vin',
    'mileage',
    'car_type_id',
    'fuel_type_id',
    'user_id',
    'city_id',
    'address',
    'phone',
    'description',
    'published_at',
])]
class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'price' => 'integer',
            'year' => 'integer',
            'mileage' => 'integer',
        ];
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Maker::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Model::class);
    }

    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function features(): HasOne
    {
        return $this->hasOne(CarFeatures::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImages::class);
    }
}
