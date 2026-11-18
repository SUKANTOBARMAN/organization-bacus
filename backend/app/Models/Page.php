<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFiles;

    public array $fileFields = ['image'];

    protected $fillable = ['slug', 'title', 'body', 'image', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];
}
