<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\PostImage;
use Illuminate\Database\Eloquent\Model;

class PostImageController extends CrudController
{
    protected string $model = PostImage::class;
    protected array $filters = ['post_id'];

    protected function rules(?Model $record = null): array
    {
        return [
            'caption' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function applyOrder($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
