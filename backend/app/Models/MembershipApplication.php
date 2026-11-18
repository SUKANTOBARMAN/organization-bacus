<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipApplication extends Model
{
    use HasFiles;

    public array $fileFields = ['photo'];

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'profession', 'workplace', 'blood_group',
        'membership_type', 'message', 'photo', 'status', 'member_id', 'reviewed_by',
        'reviewed_at', 'review_note', 'ip',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
