<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'subject', 'subject_id', 'summary', 'ip'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** কে কী করল — সংক্ষিপ্ত রেকর্ড রাখে */
    public static function record(string $action, string $subject, $subjectId = null, ?string $summary = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject' => $subject,
            'subject_id' => $subjectId,
            'summary' => $summary ? mb_substr($summary, 0, 250) : null,
            'ip' => request()->ip(),
        ]);
    }
}
