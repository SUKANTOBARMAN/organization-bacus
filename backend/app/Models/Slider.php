<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFiles;

    public array $fileFields = ['image'];

    protected $fillable = ['title', 'subtitle', 'image', 'link_url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
