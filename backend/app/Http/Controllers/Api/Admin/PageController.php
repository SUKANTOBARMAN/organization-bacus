<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PageController extends CrudController
{
    protected string $model = Page::class;
    protected array $searchable = ['title', 'slug'];

    protected function rules(?Model $record = null): array
    {
        return [
            'slug' => [$this->req($record), 'alpha_dash', 'max:100', Rule::unique('pages', 'slug')->ignore($record?->id)],
            'title' => [$this->req($record), 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
