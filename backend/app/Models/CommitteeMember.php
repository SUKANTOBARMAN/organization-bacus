<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    use HasFiles;

    public array $fileFields = ['photo'];

    protected $fillable = ['committee_id', 'member_id', 'designation_id', 'name', 'designation', 'phone', 'photo', 'sort_order'];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }
}
