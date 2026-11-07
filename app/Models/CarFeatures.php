<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'car_id',
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
])]
class CarFeatures extends Model
{
    use HasFactory;

    // protected $table = 'car_features';

    protected $primaryKey = 'car_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    protected function casts(): array
    {
        return [
            'abs' => 'boolean',
            'air_conditioning' => 'boolean',
            'power_windows' => 'boolean',
            'power_door_locks' => 'boolean',
            'cruise_control' => 'boolean',
            'bluetooth_connectivity' => 'boolean',
            'remote_start' => 'boolean',
            'gps_navigation' => 'boolean',
            'heated_seats' => 'boolean',
            'climate_control' => 'boolean',
            'rear_parking_sensors' => 'boolean',
            'leather_seats' => 'boolean',
        ];
    }
}
