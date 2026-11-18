<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Album;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlbumController extends CrudController
{
    protected string $model = Album::class;
    protected array $searchable = ['title'];
    protected array $filters = ['is_published'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'event_date' => ['nullable', 'date'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    /** POST /admin/albums/{id}/photos  (images[] = একাধিক ছবি) */
    public function upload(Request $request, $id): JsonResponse
    {
        $album = Album::findOrFail($id);
        $request->validate([
            'images' => ['required', 'array', 'max:30'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $order = (int) $album->photos()->max('sort_order');
        foreach ($request->file('images') as $image) {
            $album->photos()->create([
                'image' => $image->store('photos', 'public'),
                'sort_order' => ++$order,
            ]);
        }

        return response()->json($album->load('photos'), 201);
    }
}
