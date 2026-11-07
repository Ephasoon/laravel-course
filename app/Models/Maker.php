<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Maker extends Model
{
    use HasFactory;

    // protected $table = 'car_makers';

    public $timestamps = false;

    public function models(): HasMany
    {
        return $this->hasMany(\App\Models\Model::class);
    }
}
