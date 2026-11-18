<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name')->latest('id');

        foreach (['action', 'subject', 'user_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->query($field));
            }
        }
        if ($search = $request->query('search')) {
            $query->where('summary', 'like', "%{$search}%");
        }

        return $query->paginate(30);
    }
}
