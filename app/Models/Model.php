<?php

namespace App\Models;

use App\Observers\CatalogObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(CatalogObserver::class)]
#[Fillable(['maker_id', 'name'])]
class Model extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Maker::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
