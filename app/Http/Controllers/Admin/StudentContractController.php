<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StudentPayment;
use Illuminate\Http\Request;

class StudentContractController extends Controller
{
    public function show(Student $student)
    {
        $contract = $student->contract()->with('payments')->first();
        return view('admin.student_contracts.show', compact('student', 'contract'));
    }

    public function store(Request $request, Student $student)
    {
        $data = $request->validate([
            'total_amount'  => 'required|numeric|min:0',
            'start_date'    => 'required|date',
            'notes'         => 'nullable|string|max:1000',
            'contract_pdf'  => 'nullable|file|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('contract_pdf')) {
            $data['contract_pdf'] = uploadImage('assets/uploads/contracts', $request->file('contract_pdf'));
        }

        $data['student_id'] = $student->id;

        StudentContract::updateOrCreate(
            ['student_id' => $student->id],
            $data
        );

        return redirect()->route('admin.students.contract', $student->id)
            ->with('success', 'تم حفظ العقد بنجاح.');
    }

    public function addPayment(Request $request, Student $student)
    {
        $contract = $student->contract;

        if (! $contract) {
            return back()->with('error', 'لا يوجد عقد للطالب. أضف العقد أولاً.');
        }

        $data = $request->validate([
            'amount'  => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'notes'   => 'nullable|string|max:500',
        ]);

        $payment = StudentPayment::create([
            'student_contract_id' => $contract->id,
            'receipt_number'      => StudentPayment::generateReceiptNumber(),
            'amount'              => $data['amount'],
            'paid_at'             => $data['paid_at'],
            'notes'               => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.payments.receipt', $payment->id);
    }

    public function quickPayment(Request $request, Student $student)
    {
        $contract = $student->contract;

        if (! $contract) {
            return response()->json(['error' => 'لا يوجد عقد لهذا الطالب.'], 422);
        }

        $data = $request->validate([
            'amount'  => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'notes'   => 'nullable|string|max:500',
        ]);

        $payment = StudentPayment::create([
            'student_contract_id' => $contract->id,
            'receipt_number'      => StudentPayment::generateReceiptNumber(),
            'amount'              => $data['amount'],
            'paid_at'             => $data['paid_at'],
            'notes'               => $data['notes'] ?? null,
        ]);

        return response()->json([
            'success'        => true,
            'receipt_url'    => route('admin.payments.receipt', $payment->id),
            'receipt_number' => $payment->receipt_number,
        ]);
    }

    public function deletePayment(StudentPayment $payment)
    {
        $student = $payment->contract->student;
        $payment->delete();
        return redirect()->route('admin.students.contract', $student->id)
            ->with('success', 'تم حذف الدفعة.');
    }

    public function receipt(StudentPayment $payment)
    {
        $payment->load(['contract.student']);
        return view('admin.student_contracts.receipt', compact('payment'));
    }
}
