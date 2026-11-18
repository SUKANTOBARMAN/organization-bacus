<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFiles;

    public array $fileFields = ['file'];

    protected $fillable = ['title', 'type', 'description', 'file', 'sort_order', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];
}
