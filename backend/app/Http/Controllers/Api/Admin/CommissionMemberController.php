<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\CommissionMember;
use Illuminate\Database\Eloquent\Model;

class CommissionMemberController extends CrudController
{
    protected string $model = CommissionMember::class;
    protected array $searchable = ['name', 'designation'];

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => [$this->req($record), 'string', 'max:255'],
            'designation' => [$this->req($record), 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_current' => ['sometimes', 'boolean'],
        ];
    }
}
