<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'url', 'description', 'sort_order', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    protected $appends = ['embed_url'];

    public function getEmbedUrlAttribute(): ?string
    {
        if (preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([A-Za-z0-9_-]{11})~', (string) $this->url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return null;
    }
}
