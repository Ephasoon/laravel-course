<?php

namespace App\Models;

use App\Observers\CarImageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(CarImageObserver::class)]
#[Fillable(['car_id', 'image_path', 'position'])]
class CarImages extends Model
{
    use HasFactory;

    // protected $table = 'car_images';

    public $timestamps = false;

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
