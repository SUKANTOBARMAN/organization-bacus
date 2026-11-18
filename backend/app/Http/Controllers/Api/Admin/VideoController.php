<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Video;
use Illuminate\Database\Eloquent\Model;

class VideoController extends CrudController
{
    protected string $model = Video::class;
    protected array $searchable = ['title'];
    protected array $filters = ['is_published'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'url' => [$this->req($record), 'url', 'max:500'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    protected function applyOrder($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}
