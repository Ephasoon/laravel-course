<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class FuelType extends Model
{
    use HasFactory;

    // protected $table = 'car_fuel_types';

    public $timestamps = false;

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
