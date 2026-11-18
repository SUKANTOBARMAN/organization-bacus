<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use HasFiles;

    public array $fileFields = ['image'];

    protected $fillable = ['post_id', 'image', 'caption', 'sort_order'];
}
