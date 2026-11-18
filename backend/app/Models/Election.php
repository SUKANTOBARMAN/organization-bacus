<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    use HasFiles;

    public array $fileFields = ['result_file'];

    protected $fillable = [
        'title', 'description', 'nomination_date', 'election_date',
        'status', 'result', 'result_file', 'is_published',
    ];

    protected $casts = [
        'nomination_date' => 'date:Y-m-d',
        'election_date' => 'date:Y-m-d',
        'is_published' => 'boolean',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(ElectionCandidate::class)->orderBy('position')->orderByDesc('votes');
    }
}
