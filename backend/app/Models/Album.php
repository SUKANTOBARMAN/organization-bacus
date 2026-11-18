<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFiles;

    public array $fileFields = ['cover_image'];

    protected $fillable = ['title', 'description', 'cover_image', 'event_date', 'is_published'];

    protected $casts = ['event_date' => 'date:Y-m-d', 'is_published' => 'boolean'];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('sort_order')->orderBy('id');
    }
}
