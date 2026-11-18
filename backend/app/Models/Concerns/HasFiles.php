<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * মডেলে $fileFields = ['photo'] থাকলে JSON আউটপুটে photo_url যোগ হয়
 * এবং রেকর্ড ডিলিট হলে ফাইলও মুছে যায়।
 */
trait HasFiles
{
    public static function bootHasFiles(): void
    {
        static::deleting(function ($model) {
            foreach ($model->fileFields ?? [] as $field) {
                if ($model->{$field}) {
                    Storage::disk('public')->delete($model->{$field});
                }
            }
        });
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        foreach ($this->fileFields ?? [] as $field) {
            if (array_key_exists($field, $array)) {
                $array[$field.'_url'] = $array[$field]
                    ? Storage::disk('public')->url($array[$field])
                    : null;
            }
        }

        return $array;
    }
}
