<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query()->latest('id');

        if ($request->query('unread')) {
            $query->whereNull('read_at');
        }

        return $query->paginate(20);
    }

    public function show($id): JsonResponse
    {
        $message = ContactMessage::findOrFail($id);
        $message->read_at ??= now();
        $message->save();

        return response()->json($message);
    }

    public function destroy($id): Response
    {
        ContactMessage::findOrFail($id)->delete();

        return response()->noContent();
    }
}
