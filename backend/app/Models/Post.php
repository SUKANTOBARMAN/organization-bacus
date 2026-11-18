<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFiles;

    public array $fileFields = ['cover_image'];

    protected $fillable = [
        'title', 'type', 'excerpt', 'body', 'cover_image', 'event_at',
        'venue', 'published_at', 'is_published', 'is_featured', 'video_url', 'created_by',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'published_at' => 'date:Y-m-d',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class)->orderBy('sort_order')->orderBy('id');
    }
}
