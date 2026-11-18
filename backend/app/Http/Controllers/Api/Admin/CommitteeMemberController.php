<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\CommitteeMember;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CommitteeMemberController extends CrudController
{
    protected string $model = CommitteeMember::class;
    protected array $searchable = ['name', 'designation'];
    protected array $filters = ['committee_id'];

    protected function rules(?Model $record = null): array
    {
        return [
            'committee_id' => [$this->req($record), 'exists:committees,id'],
            'member_id' => ['nullable', 'exists:members,id'],
            'name' => [$this->req($record), 'string', 'max:255'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'designation' => [$record ? 'sometimes' : 'required_without:designation_id', 'nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function applyOrder($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    protected function beforeCreate(array $data, Request $request): array
    {
        return $this->syncFromDesignation($data);
    }

    protected function beforeUpdate(array $data, Request $request, Model $record): array
    {
        return $this->syncFromDesignation($data);
    }

    /** পদবি বেছে নিলে নাম ও ক্রম (র‍্যাঙ্ক) স্বয়ংক্রিয়ভাবে বসে */
    private function syncFromDesignation(array $data): array
    {
        if (! empty($data['designation_id']) && ($designation = Designation::find($data['designation_id']))) {
            $data['designation'] = $data['designation'] ?? $designation->name;
            $data['sort_order'] = $data['sort_order'] ?? $designation->rank;
        }
        $data['designation'] = $data['designation'] ?? '';

        return $data;
    }
}
