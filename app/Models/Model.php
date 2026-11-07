<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

#[Fillable(['maker_id', 'name'])]
class Model extends EloquentModel
{
    use HasFactory;

    public $timestamps = false;
}
