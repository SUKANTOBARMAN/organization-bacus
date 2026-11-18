<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPayment extends Model
{
    protected $fillable = ['member_id', 'type', 'amount', 'paid_at', 'period', 'method', 'reference', 'note', 'recorded_by'];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'date:Y-m-d'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
