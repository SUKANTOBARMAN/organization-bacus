<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

/**
 * সব অ্যাডমিন CRUD এন্ডপয়েন্টের ভিত্তি।
 * সাবক্লাসে শুধু $model, $searchable, $filters, $with আর rules() দিলেই হয়।
 *
 * ফাইলসহ আপডেট: multipart/form-data দিয়ে POST করুন এবং _method=PUT পাঠান
 * (PHP multipart PUT পার্স করে না)।
 */
abstract class CrudController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    protected array $with = [];

    protected array $searchable = [];

    protected array $filters = [];

    abstract protected function rules(?Model $record = null): array;

    protected function req(?Model $record): string
    {
        return $record ? 'sometimes' : 'required';
    }

    protected function beforeCreate(array $data, Request $request): array
    {
        return $data;
    }

    protected function beforeUpdate(array $data, Request $request, Model $record): array
    {
        return $data;
    }

    /** সাবক্লাসে বদলানো যায় (যেমন সদস্য: পদবির র‍্যাঙ্ক) */
    protected function applyOrder($query)
    {
        return $query->latest('id');
    }

    protected function log(string $action, Model $record): void
    {
        $label = $record->title ?? $record->name ?? $record->member_no ?? ('#'.$record->getKey());
        ActivityLog::record($action, class_basename($record), $record->getKey(), Str::limit((string) $label, 120));
    }

    public function index(Request $request)
    {
        $query = $this->model::query()->with($this->with);

        if ($search = $request->query('search')) {
            $query->where(function ($w) use ($search) {
                foreach ($this->searchable as $column) {
                    $w->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        // $filters: ['status'] অথবা ['status' => 'members.status'] (প্যারামিটার => কলাম)
        foreach ($this->filters as $param => $column) {
            $param = is_int($param) ? $column : $param;
            if ($request->filled($param)) {
                $query->where($column, $request->query($param));
            }
        }

        return $this->applyOrder($query)->paginate(min((int) $request->query('per_page', 20), 100));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data = $this->storeFiles($request, $data, new $this->model);
        $record = $this->model::create($this->beforeCreate($data, $request));
        $this->log('created', $record);

        return response()->json($record->load($this->with), 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json($this->model::with($this->with)->findOrFail($id));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $record = $this->model::findOrFail($id);
        $data = $request->validate($this->rules($record));
        $data = $this->storeFiles($request, $data, $record);
        $record->update($this->beforeUpdate($data, $request, $record));
        $this->log('updated', $record);

        return response()->json($record->fresh($this->with));
    }

    public function destroy($id): Response
    {
        $record = $this->model::findOrFail($id);
        $this->log('deleted', $record);
        $record->delete();

        return response()->noContent();
    }

    /** $fileFields-এর ফাইল আপলোড হ্যান্ডেল করে; পুরনো ফাইল মুছে ফেলে। */
    protected function storeFiles(Request $request, array $data, Model $record): array
    {
        foreach ($record->fileFields ?? [] as $field) {
            if ($request->hasFile($field)) {
                if ($record->exists && $record->{$field}) {
                    Storage::disk('public')->delete($record->{$field});
                }
                $data[$field] = $request->file($field)->store($record->getTable(), 'public');
            } else {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
