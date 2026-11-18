<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UserController extends CrudController
{
    protected string $model = User::class;
    protected array $searchable = ['name', 'email'];
    protected array $filters = ['role', 'is_active'];

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => [$this->req($record), 'string', 'max:255'],
            'email' => [$this->req($record), 'email', 'max:255', Rule::unique('users', 'email')->ignore($record?->id)],
            'password' => [$record ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['sometimes', 'in:super_admin,admin,editor'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function beforeUpdate(array $data, Request $request, Model $record): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    public function destroy($id): Response
    {
        abort_if((int) $id === request()->user()->id, 422, 'নিজের অ্যাকাউন্ট মুছে ফেলা যাবে না।');

        return parent::destroy($id);
    }
}
