<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = ['name', 'name_bn', 'rank', 'is_executive'];

    protected $casts = ['rank' => 'integer', 'is_executive' => 'boolean'];
}
