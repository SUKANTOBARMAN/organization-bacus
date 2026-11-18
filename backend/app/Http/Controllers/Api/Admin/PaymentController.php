<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\MemberPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends CrudController
{
    protected string $model = MemberPayment::class;
    protected array $with = ['member:id,member_no,name'];
    protected array $searchable = ['reference', 'period'];
    protected array $filters = ['type', 'member_id', 'method'];

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => [$this->req($record), 'exists:members,id'],
            'type' => ['sometimes', 'in:subscription,donation,admission_fee,other'],
            'amount' => [$this->req($record), 'numeric', 'min:0.01', 'max:99999999'],
            'paid_at' => [$this->req($record), 'date'],
            'period' => ['nullable', 'string', 'max:20'],
            'method' => ['sometimes', 'in:cash,bkash,nagad,bank,other'],
            'reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ];
    }

    protected function beforeCreate(array $data, Request $request): array
    {
        $data['recorded_by'] = $request->user()->id;

        return $data;
    }

    protected function applyOrder($query)
    {
        return $query->orderByDesc('paid_at')->orderByDesc('id');
    }

    /** GET /admin/payments/summary?year=2026 — ধরন ও মাসভিত্তিক মোট */
    public function summary(Request $request): JsonResponse
    {
        $year = (int) $request->query('year', now()->year);
        $rows = MemberPayment::whereYear('paid_at', $year)->get(['type', 'amount', 'paid_at']);

        $byMonth = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $byMonth[(int) $row->paid_at->format('n')] += (float) $row->amount;
        }

        return response()->json([
            'year' => $year,
            'total' => round((float) $rows->sum('amount'), 2),
            'by_type' => $rows->groupBy('type')->map(fn ($g) => round((float) $g->sum('amount'), 2)),
            'by_month' => array_values(array_map(fn ($v) => round($v, 2), $byMonth)),
        ]);
    }
}
