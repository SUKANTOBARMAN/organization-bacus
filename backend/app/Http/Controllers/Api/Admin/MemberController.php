<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends CrudController
{
    protected string $model = Member::class;
    protected array $with = ['position'];
    protected array $searchable = ['members.name', 'members.member_no', 'members.phone', 'members.email', 'members.profession'];
    protected array $filters = [
        'status' => 'members.status',
        'is_public' => 'members.is_public',
        'membership_type' => 'members.membership_type',
        'blood_group' => 'members.blood_group',
        'designation_id' => 'members.designation_id',
    ];

    protected function rules(?Model $record = null): array
    {
        return [
            'member_no' => [$this->req($record), 'string', 'max:30', Rule::unique('members', 'member_no')->ignore($record?->id)],
            'name' => [$this->req($record), 'string', 'max:255'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'designation' => ['nullable', 'string', 'max:255'],
            'membership_type' => ['sometimes', 'in:general,life,associate,honorary'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'profession' => ['nullable', 'string', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'joined_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:active,inactive'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    /** পদবির র‍্যাঙ্ক অনুযায়ী সাজানো */
    protected function applyOrder($query)
    {
        return $query->orderedByRank();
    }

    /** GET /admin/members/export — Excel-এ খোলা যায় এমন CSV (UTF-8 BOM সহ) */
    public function export(Request $request): StreamedResponse
    {
        $members = Member::orderedByRank()->with('position')->get();

        return response()->streamDownload(function () use ($members) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Member No', 'Name', 'Designation', 'Type', 'Phone', 'Email', 'Blood group', 'Profession', 'Workplace', 'Address', 'Joined', 'Status']);
            foreach ($members as $m) {
                fputcsv($out, [
                    $m->member_no, $m->name, $m->position?->name ?? $m->designation, $m->membership_type,
                    $m->phone, $m->email, $m->blood_group, $m->profession, $m->workplace, $m->address,
                    $m->joined_at?->format('Y-m-d'), $m->status,
                ]);
            }
            fclose($out);
        }, 'members-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
