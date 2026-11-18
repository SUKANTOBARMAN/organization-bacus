<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Model;

class PhotoController extends CrudController
{
    protected string $model = Photo::class;
    protected array $filters = ['album_id'];

    protected function rules(?Model $record = null): array
    {
        return [
            'album_id' => [$this->req($record), 'exists:albums,id'],
            'image' => [$record ? 'sometimes' : 'required', 'image', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
