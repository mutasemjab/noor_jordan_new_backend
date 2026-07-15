@extends('admin.layouts.app')
@section('title', 'عقد الطالب — ' . $student->name)

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">عقد الطالب</h1>
        <p class="page-sub">{{ $student->name }}</p>
    </div>
    <a href="{{ route('admin.students.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> رجوع</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">

    {{-- Contract form --}}
    <div class="col-12 col-xl-5">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ $contract ? 'تعديل العقد' : 'إنشاء عقد جديد' }}</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.students.contract.store', $student->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">المبلغ الإجمالي (دينار) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" step="0.01" min="0"
                               value="{{ old('total_amount', $contract?->total_amount) }}"
                               class="form-control @error('total_amount') is-invalid @enderror" required>
                        @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', $contract?->start_date?->format('Y-m-d')) }}"
                               class="form-control @error('start_date') is-invalid @enderror" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $contract?->notes) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملف العقد PDF</label>
                        <input type="file" name="contract_pdf" accept=".pdf" class="form-control">
                        @if($contract?->contract_pdf)
                            <div class="mt-1" style="font-size:.82rem">
                                الملف الحالي:
                                <a href="{{ asset('assets/uploads/contracts/'.$contract->contract_pdf) }}" target="_blank" class="text-danger">
                                    <i class="bi bi-file-earmark-pdf"></i> عرض العقد
                                </a>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                        <i class="bi bi-save"></i> حفظ العقد
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Payments section --}}
    <div class="col-12 col-xl-7">
        @if($contract)
        {{-- Summary --}}
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="stat-card" style="padding:16px">
                    <div style="font-size:1.4rem;font-weight:800;color:var(--primary)">{{ number_format($contract->total_amount, 2) }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">إجمالي العقد (د.أ)</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card" style="padding:16px">
                    <div style="font-size:1.4rem;font-weight:800;color:#059669">{{ number_format($contract->paid_amount, 2) }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">المدفوع (د.أ)</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card" style="padding:16px">
                    <div style="font-size:1.4rem;font-weight:800;color:#dc2626">{{ number_format($contract->remaining_amount, 2) }}</div>
                    <div style="font-size:.75rem;color:var(--muted)">المتبقي (د.أ)</div>
                </div>
            </div>
        </div>

        {{-- Add payment --}}
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">تسجيل دفعة جديدة</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.students.payments.store', $student->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">المبلغ (د.أ) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label>
                            <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ملاحظات</label>
                            <input type="text" name="notes" class="form-control" placeholder="اختياري">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-primary-sm">
                                <i class="bi bi-plus-circle"></i> تسجيل الدفعة وطباعة الإيصال
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Payments list --}}
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">سجل الدفعات ({{ $contract->payments->count() }})</h2></div>
            <div class="panel-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>رقم الإيصال</th><th>المبلغ</th><th>تاريخ الدفع</th><th>ملاحظات</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($contract->payments as $payment)
                        <tr>
                            <td><span style="font-family:monospace;font-size:.82rem">{{ $payment->receipt_number }}</span></td>
                            <td style="font-weight:600;color:#059669">{{ number_format($payment->amount, 2) }} د.أ</td>
                            <td style="color:var(--muted)">{{ $payment->paid_at->format('Y-m-d') }}</td>
                            <td style="color:var(--muted);font-size:.82rem">{{ $payment->notes ?: '—' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-printer"></i></a>
                                    <form action="{{ route('admin.payments.delete', $payment->id) }}" method="POST" onsubmit="return confirm('حذف هذه الدفعة؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4" style="color:var(--muted)">لا توجد دفعات مسجلة بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="panel-card">
            <div class="panel-card-body text-center py-5">
                <i class="bi bi-file-earmark-text" style="font-size:3rem;color:var(--muted)"></i>
                <p class="mt-3" style="color:var(--muted)">لا يوجد عقد لهذا الطالب بعد. أنشئ العقد من النموذج على اليسار.</p>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
