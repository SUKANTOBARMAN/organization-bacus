<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Election;
use Illuminate\Database\Eloquent\Model;

class ElectionController extends CrudController
{
    protected string $model = Election::class;
    protected array $searchable = ['title'];
    protected array $filters = ['status', 'is_published'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'nomination_date' => ['nullable', 'date'],
            'election_date' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:upcoming,ongoing,completed'],
            'result' => ['nullable', 'string'],
            'result_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
