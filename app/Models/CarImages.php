<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['car_id', 'image_path', 'position'])]
class CarImages extends Model
{
    use HasFactory;

    // protected $table = 'car_images';

    public $timestamps = false;
}
