@extends('admin.layouts.app')
@section('title', __('messages.students_title'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.students_title') }}</h1><p class="page-sub">{{ __('messages.manage_students_desc') }}</p></div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.students.export') }}?{{ http_build_query(request()->all()) }}" class="btn-outline-sm">
            <i class="bi bi-file-earmark-arrow-down"></i> تصدير Excel
        </a>
        <button class="btn-outline-sm" type="button" data-bs-toggle="collapse" data-bs-target="#import-panel">
            <i class="bi bi-file-earmark-arrow-up"></i> استيراد Excel
        </button>
        <a href="{{ route('admin.students.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('messages.add_student') }}</a>
    </div>
</div>

{{-- Import panel --}}
<div class="collapse mb-3" id="import-panel">
    <div class="panel-card">
        <div class="panel-card-header"><h2 class="panel-card-title"><i class="bi bi-file-earmark-arrow-up"></i> استيراد الطلاب من Excel</h2></div>
        <div class="panel-card-body">
            <p class="text-muted small mb-2">الأعمدة المطلوبة: <strong>الاسم</strong> (إلزامي)، الرقم_الوطني، البريد_الإلكتروني، الهاتف، الصف، كلمة_المرور (افتراضي: Pass@1234)</p>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-end flex-wrap">
                @csrf
                <div>
                    <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                </div>
                <button type="submit" class="btn-primary-sm"><i class="bi bi-upload"></i> استيراد</button>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('messages.search_name_email_ph') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="is_active" class="form-select form-select-sm">
                    <option value="">{{ __('messages.All Status') }}</option>
                    <option value="1" @selected(request('is_active') === '1')>{{ __('messages.Active') }}</option>
                    <option value="0" @selected(request('is_active') === '0')>{{ __('messages.Inactive') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn-primary-sm w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="panel-card">
    <div class="panel-card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.student') }}</th>
                    <th>{{ __('messages.phone_label') }}</th>
                    <th>العقد</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th>{{ __('messages.joined') }}</th>
                    <th>{{ __('messages.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="color:var(--muted)">{{ $student->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($student->avatar)
                                <img src="{{ asset('assets/uploads/students/'.$student->avatar) }}" class="avatar avatar-sm" alt="">
                            @else
                                <div class="avatar avatar-sm" style="background:#f5f3ff;color:#7c3aed">{{ strtoupper(substr($student->name,0,1)) }}</div>
                            @endif
                            <div>
                                <div style="font-weight:500">{{ $student->name }}</div>
                                <div style="font-size:.75rem;color:var(--muted)">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--muted)">{{ $student->phone ?: '—' }}</td>
                    <td>
                        <a href="{{ route('admin.students.contract', $student->id) }}" class="btn-outline-sm" style="padding:4px 10px;font-size:.78rem">
                            <i class="bi bi-file-earmark-text"></i> عقد
                        </a>
                    </td>
                    <td><span class="pill {{ $student->is_active ? 'pill-success' : 'pill-neutral' }}">{{ $student->is_active ? __('messages.Active') : __('messages.Inactive') }}</span></td>
                    <td style="color:var(--muted)">{{ $student->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button"
                                    class="btn-outline-sm btn-quick-pay"
                                    style="padding:4px 8px;color:#059669;border-color:#bbf7d0"
                                    data-student-id="{{ $student->id }}"
                                    data-student-name="{{ $student->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#quickPayModal">
                                <i class="bi bi-cash-coin"></i>
                            </button>
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn-outline-sm" style="padding:4px 8px"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.Delete') }}?')">
                                @csrf @method('DELETE')
                                <button class="btn-outline-sm" style="padding:4px 8px;color:#dc2626;border-color:#fecaca"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">{{ __('messages.no_students_found') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $students->withQueryString()->links() }}</div>
    </div>
</div>

{{-- Quick Payment Modal --}}
<div class="modal fade" id="quickPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15)">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title" style="font-weight:700">تسجيل دفعة</h5>
                    <p class="mb-0" id="qp-student-name" style="font-size:.85rem;color:var(--muted)"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div id="qp-error" class="alert alert-danger d-none mb-3"></div>
                <div id="qp-success" class="alert alert-success d-none mb-3"></div>
                <form id="quickPayForm">
                    @csrf
                    <input type="hidden" id="qp-student-id" name="student_id">
                    <div class="mb-3">
                        <label class="form-label">المبلغ (د.أ) <span class="text-danger">*</span></label>
                        <input type="number" id="qp-amount" name="amount" step="0.01" min="0.01" class="form-control" required placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label>
                        <input type="date" id="qp-date" name="paid_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <input type="text" id="qp-notes" name="notes" class="form-control" placeholder="اختياري">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-outline-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="qp-submit" class="btn-primary-sm">
                    <i class="bi bi-cash-coin"></i> تسجيل وطباعة الإيصال
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('quickPayModal');
    if (!modal) return;

    @php
        $quickPayTemplate = route('admin.students.quick-payment', '__SID__');
    @endphp
    const quickPayTemplate = @json($quickPayTemplate);

    // Set student info when modal opens
    modal.addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('qp-student-id').value = btn.dataset.studentId;
        document.getElementById('qp-student-name').textContent = btn.dataset.studentName;
        document.getElementById('qp-amount').value = '';
        document.getElementById('qp-notes').value = '';
        document.getElementById('qp-error').classList.add('d-none');
        document.getElementById('qp-success').classList.add('d-none');
    });

    document.getElementById('qp-submit').addEventListener('click', async function () {
        const studentId = document.getElementById('qp-student-id').value;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

        const url = quickPayTemplate.replace('__SID__', studentId);

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: new URLSearchParams({
                    amount:  document.getElementById('qp-amount').value,
                    paid_at: document.getElementById('qp-date').value,
                    notes:   document.getElementById('qp-notes').value,
                }),
            });

            const json = await res.json();

            if (json.success) {
                const successEl = document.getElementById('qp-success');
                successEl.innerHTML = `تم تسجيل الدفعة بنجاح! رقم الإيصال: <strong>${json.receipt_number}</strong>`;
                successEl.classList.remove('d-none');
                document.getElementById('qp-error').classList.add('d-none');
                // Open receipt in new tab
                window.open(json.receipt_url, '_blank');
                setTimeout(() => {
                    bootstrap.Modal.getInstance(modal).hide();
                }, 1500);
            } else {
                const errEl = document.getElementById('qp-error');
                errEl.textContent = json.error || json.message || 'حدث خطأ ما.';
                errEl.classList.remove('d-none');
            }
        } catch (err) {
            const errEl = document.getElementById('qp-error');
            errEl.textContent = 'فشل الاتصال بالخادم.';
            errEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cash-coin"></i> تسجيل وطباعة الإيصال';
        }
    });
})();
</script>
@endpush

@endsection
