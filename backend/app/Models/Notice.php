<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFiles;

    public array $fileFields = ['file'];

    protected $fillable = [
        'title', 'category', 'body', 'file', 'published_at',
        'is_published', 'is_pinned', 'created_by',
    ];

    protected $casts = [
        'published_at' => 'date:Y-m-d',
        'is_published' => 'boolean',
        'is_pinned' => 'boolean',
    ];
}
