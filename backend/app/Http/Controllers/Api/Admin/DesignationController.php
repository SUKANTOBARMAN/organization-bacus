<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Designation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class DesignationController extends CrudController
{
    protected string $model = Designation::class;
    protected array $searchable = ['name', 'name_bn'];

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => [$this->req($record), 'string', 'max:255', Rule::unique('designations', 'name')->ignore($record?->id)],
            'name_bn' => ['nullable', 'string', 'max:255'],
            'rank' => ['sometimes', 'integer', 'between:1,9999'],
            'is_executive' => ['sometimes', 'boolean'],
        ];
    }

    protected function applyOrder($query)
    {
        return $query->orderBy('rank')->orderBy('name');
    }
}
