<?php

namespace App\Models;

use App\Observers\CatalogObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(CatalogObserver::class)]
#[Fillable(['name'])]
class State extends Model
{
    use HasFactory;

    // protected $table = 'car_states';

    public $timestamps = false;

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
