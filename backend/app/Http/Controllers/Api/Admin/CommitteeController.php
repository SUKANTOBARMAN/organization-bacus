<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Committee;
use Illuminate\Database\Eloquent\Model;

class CommitteeController extends CrudController
{
    protected string $model = Committee::class;
    protected array $with = ['members'];
    protected array $searchable = ['title'];
    protected array $filters = ['type', 'is_current'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'type' => ['sometimes', 'in:executive,advisory,sub_committee'],
            'term_start' => [$this->req($record), 'integer', 'between:1950,2100'],
            'term_end' => [$this->req($record), 'integer', 'between:1950,2100', 'gte:term_start'],
            'is_current' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
        ];
    }
}
