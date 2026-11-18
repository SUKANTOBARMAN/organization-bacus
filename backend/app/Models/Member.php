<?php

namespace App\Models;

use App\Models\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFiles;

    public array $fileFields = ['photo'];

    protected $fillable = [
        'member_no', 'name', 'designation', 'email', 'phone', 'address',
        'photo', 'joined_at', 'status', 'is_public', 'designation_id', 'membership_type',
        'blood_group', 'profession', 'workplace', 'gender', 'date_of_birth', 'bio',
    ];

    protected $casts = [
        'joined_at' => 'date:Y-m-d',
        'date_of_birth' => 'date:Y-m-d',
        'is_public' => 'boolean',
    ];

    /** পাবলিক ডিরেক্টরিতে যাদের দেখানো যাবে */
    public function scopePublicListed(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('is_public', true);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /** পদবির র‍্যাঙ্ক অনুযায়ী (President আগে), তারপর নাম অনুযায়ী */
    public function scopeOrderedByRank(Builder $query): Builder
    {
        return $query->leftJoin('designations', 'designations.id', '=', 'members.designation_id')
            ->select('members.*')
            ->orderByRaw('COALESCE(designations.rank, 9999)')
            ->orderBy('members.name');
    }

    /** পরবর্তী সদস্য নম্বর: 0001, 0002 ... */
    public static function nextNumber(): string
    {
        $max = static::pluck('member_no')->filter(fn ($n) => ctype_digit((string) $n))->map(fn ($n) => (int) $n)->max();

        return str_pad((string) (($max ?? 0) + 1), 4, '0', STR_PAD_LEFT);
    }
}
