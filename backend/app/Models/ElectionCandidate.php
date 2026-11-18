<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class ElectionCandidate extends Model
{
    use HasFiles;

    public array $fileFields = ['photo'];

    protected $fillable = ['election_id', 'member_id', 'name', 'position', 'photo', 'votes', 'is_winner'];

    protected $casts = ['votes' => 'integer', 'is_winner' => 'boolean'];
}
