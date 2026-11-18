<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends CrudController
{
    protected string $model = Post::class;
    protected array $with = ['images'];
    protected array $searchable = ['title', 'excerpt'];
    protected array $filters = ['type', 'is_published', 'is_featured'];

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => [$this->req($record), 'string', 'max:255'],
            'type' => ['sometimes', 'in:news,event'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'event_at' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }

    protected function beforeCreate(array $data, Request $request): array
    {
        $data['created_by'] = $request->user()->id;
        $data['published_at'] ??= now()->toDateString();

        return $data;
    }

    /** POST /admin/posts/{id}/images (images[] = একাধিক ছবি) */
    public function upload(Request $request, $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $request->validate([
            'images' => ['required', 'array', 'max:20'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $order = (int) $post->images()->max('sort_order');
        foreach ($request->file('images') as $image) {
            $post->images()->create(['image' => $image->store('post_images', 'public'), 'sort_order' => ++$order]);
        }

        return response()->json($post->load('images'), 201);
    }
}
