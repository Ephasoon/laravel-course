<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class CarType extends Model
{
    use HasFactory;

    // protected $table = 'car_types';

    public $timestamps = false;
}
