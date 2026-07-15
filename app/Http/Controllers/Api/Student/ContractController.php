<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function show(Request $request)
    {
        $student  = $request->user();
        $contract = $student->contract()->with('payments')->first();

        if (! $contract) {
            return response()->json([
                'status'  => false,
                'message' => 'لا يوجد عقد مسجل لهذا الطالب.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'تم جلب بيانات العقد بنجاح.',
            'data'    => [
                'contract' => [
                    'id'               => $contract->id,
                    'total_amount'     => (float) $contract->total_amount,
                    'paid_amount'      => (float) $contract->paid_amount,
                    'remaining_amount' => (float) $contract->remaining_amount,
                    'start_date'       => $contract->start_date?->format('Y-m-d'),
                    'notes'            => $contract->notes,
                    'contract_pdf'     => $contract->contract_pdf
                        ? asset('assets/uploads/contracts/' . $contract->contract_pdf)
                        : null,
                ],
                'payments' => $contract->payments->map(fn ($p) => [
                    'id'             => $p->id,
                    'receipt_number' => $p->receipt_number,
                    'amount'         => (float) $p->amount,
                    'paid_at'        => $p->paid_at->format('Y-m-d'),
                    'notes'          => $p->notes,
                ]),
            ],
        ]);
    }
}
