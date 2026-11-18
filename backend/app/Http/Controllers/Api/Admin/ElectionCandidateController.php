<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ElectionCandidate;
use Illuminate\Database\Eloquent\Model;

class ElectionCandidateController extends CrudController
{
    protected string $model = ElectionCandidate::class;
    protected array $searchable = ['name', 'position'];
    protected array $filters = ['election_id'];

    protected function rules(?Model $record = null): array
    {
        return [
            'election_id' => [$this->req($record), 'exists:elections,id'],
            'member_id' => ['nullable', 'exists:members,id'],
            'name' => [$this->req($record), 'string', 'max:255'],
            'position' => [$this->req($record), 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'votes' => ['sometimes', 'integer', 'min:0'],
            'is_winner' => ['sometimes', 'boolean'],
        ];
    }

    protected function applyOrder($query)
    {
        return $query->orderBy('position')->orderByDesc('votes');
    }
}
