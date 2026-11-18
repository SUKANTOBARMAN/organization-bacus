<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Database\Eloquent\Model;

class SliderController extends CrudController
{
    protected string $model = Slider::class;
    protected array $searchable = ['title'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => [$record ? 'sometimes' : 'required', 'image', 'max:6144'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function applyOrder($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
