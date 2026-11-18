<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class NoticeController extends CrudController
{
    protected string $model = Notice::class;
    protected array $searchable = ['title'];
    protected array $filters = ['category', 'is_published'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'category' => ['sometimes', 'in:notice,circular,tender,result,press_release'],
            'body' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['sometimes', 'boolean'],
            'is_pinned' => ['sometimes', 'boolean'],
        ];
    }

    protected function beforeCreate(array $data, Request $request): array
    {
        $data['created_by'] = $request->user()->id;
        $data['published_at'] ??= now()->toDateString();

        return $data;
    }
}
