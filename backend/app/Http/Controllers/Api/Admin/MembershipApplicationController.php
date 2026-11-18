<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\MembershipApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MembershipApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipApplication::with('member:id,member_no,name')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($search = $request->query('search')) {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
        }

        return $query->paginate(20);
    }

    public function show($id): JsonResponse
    {
        return response()->json(MembershipApplication::with('member:id,member_no,name')->findOrFail($id));
    }

    /** অনুমোদন: আবেদন থেকে নতুন সদস্য তৈরি হয় */
    public function approve(Request $request, $id): JsonResponse
    {
        $application = MembershipApplication::findOrFail($id);
        abort_if($application->status !== 'pending', 422, 'এই আবেদন আগেই নিষ্পত্তি হয়েছে।');

        $data = $request->validate([
            'designation_id' => ['nullable', 'exists:designations,id'],
            'review_note' => ['nullable', 'string'],
        ]);

        $member = DB::transaction(function () use ($application, $data, $request) {
            $photo = null;
            if ($application->photo && Storage::disk('public')->exists($application->photo)) {
                $photo = 'members/'.basename($application->photo);
                Storage::disk('public')->copy($application->photo, $photo);
            }

            $member = Member::create([
                'member_no' => Member::nextNumber(),
                'name' => $application->name,
                'email' => $application->email,
                'phone' => $application->phone,
                'address' => $application->address,
                'profession' => $application->profession,
                'workplace' => $application->workplace,
                'blood_group' => $application->blood_group,
                'membership_type' => $application->membership_type,
                'designation_id' => $data['designation_id'] ?? null,
                'photo' => $photo,
                'joined_at' => now()->toDateString(),
                'status' => 'active',
                'is_public' => true,
            ]);

            $application->update([
                'status' => 'approved',
                'member_id' => $member->id,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_note' => $data['review_note'] ?? null,
            ]);

            return $member;
        });

        ActivityLog::record('approved', 'MembershipApplication', $application->id, "{$application->name} → #{$member->member_no}");

        return response()->json($application->fresh()->load('member:id,member_no,name'));
    }

    public function reject(Request $request, $id): JsonResponse
    {
        $application = MembershipApplication::findOrFail($id);
        abort_if($application->status !== 'pending', 422, 'এই আবেদন আগেই নিষ্পত্তি হয়েছে।');

        $data = $request->validate(['review_note' => ['nullable', 'string']]);

        $application->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        ActivityLog::record('rejected', 'MembershipApplication', $application->id, $application->name);

        return response()->json($application->fresh());
    }

    public function destroy($id): Response
    {
        MembershipApplication::findOrFail($id)->delete();

        return response()->noContent();
    }
}
