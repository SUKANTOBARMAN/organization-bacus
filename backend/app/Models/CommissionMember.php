<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class CommissionMember extends Model
{
    use HasFiles;

    public array $fileFields = ['photo'];

    protected $fillable = ['name', 'designation', 'phone', 'photo', 'sort_order', 'is_current'];

    protected $casts = ['is_current' => 'boolean'];
}
