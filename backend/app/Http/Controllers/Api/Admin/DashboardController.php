<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Album;
use App\Models\ContactMessage;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\MembershipApplication;
use App\Models\Notice;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = [
            'notices' => Notice::count(),
            'posts' => Post::count(),
            'albums' => Album::count(),
            'upcoming_events' => Post::where('type', 'event')->where('event_at', '>=', now())->count(),
        ];

        if ($request->user()->isRole('super_admin', 'admin')) {
            $data += [
                'members' => Member::where('status', 'active')->count(),
                'pending_applications' => MembershipApplication::where('status', 'pending')->count(),
                'unread_messages' => ContactMessage::whereNull('read_at')->count(),
                'payments_this_year' => (float) MemberPayment::whereYear('paid_at', now()->year)->sum('amount'),
                'recent_messages' => ContactMessage::latest('id')->limit(5)->get(['id', 'name', 'subject', 'created_at', 'read_at']),
                'recent_activity' => ActivityLog::with('user:id,name')->latest('id')->limit(8)->get(),
            ];
        }

        return response()->json($data);
    }
}
