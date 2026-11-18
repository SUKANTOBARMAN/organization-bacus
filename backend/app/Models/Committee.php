<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Committee extends Model
{
    protected $fillable = ['title', 'type', 'term_start', 'term_end', 'is_current', 'description'];

    protected $casts = ['is_current' => 'boolean'];

    protected $appends = ['term'];

    protected static function booted(): void
    {
        // একসময়ে শুধু একটি কমিটি "current" থাকবে
        static::saved(function (Committee $committee) {
            if ($committee->is_current) {
                static::where('id', '!=', $committee->id)->update(['is_current' => false]);
            }
        });
    }

    public function getTermAttribute(): string
    {
        return $this->term_start.'–'.$this->term_end;
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class)->orderBy('sort_order')->orderBy('id');
    }
}
