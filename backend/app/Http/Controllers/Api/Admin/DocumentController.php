<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;

class DocumentController extends CrudController
{
    protected string $model = Document::class;
    protected array $searchable = ['title'];
    protected array $filters = ['type', 'is_published'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'type' => ['sometimes', 'in:constitution,rules,minutes,annual_report,form,download'],
            'description' => ['nullable', 'string'],
            'file' => [$record ? 'sometimes' : 'required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:20480'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
