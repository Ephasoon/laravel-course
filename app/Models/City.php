<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['state_id', 'name'])]
class City extends Model
{
    use HasFactory;

    // protected $table = 'car_cities';

    public $timestamps = false;
}
