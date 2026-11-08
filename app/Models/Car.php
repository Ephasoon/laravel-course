<?php

namespace App\Models;

use App\Observers\CarObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CarObserver::class)]
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

    public function primaryImage(): HasOne
    {
        return $this->hasOne(CarImages::class)->ofMany('position', 'min');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $filled = fn (string $key): bool => ! blank($filters[$key] ?? null);

        foreach (['maker_id', 'model_id', 'car_type_id', 'fuel_type_id', 'city_id'] as $filter) {
            if ($filled($filter)) {
                $query->where($filter, $filters[$filter]);
            }
        }

        if ($filled('state_id')) {
            $query->whereHas('city', fn (Builder $cityQuery) => $cityQuery->where('state_id', $filters['state_id']));
        }

        if ($filled('year_from')) {
            $query->where('year', '>=', (int) $filters['year_from']);
        }

        if ($filled('year_to')) {
            $query->where('year', '<=', (int) $filters['year_to']);
        }

        if ($filled('price_from')) {
            $query->where('price', '>=', $filters['price_from']);
        }

        if ($filled('price_to')) {
            $query->where('price', '<=', $filters['price_to']);
        }

        if ($filled('mileage')) {
            $query->where('mileage', '<=', (int) $filters['mileage']);
        }

        if ($filled('sort')) {
            $sort = $filters['sort'];
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $query->orderBy(ltrim($sort, '-'), $direction);
        } else {
            $query->orderByDesc('published_at');
        }

        return $query;
    }
}
