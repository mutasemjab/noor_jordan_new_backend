@extends('admin.layouts.app')
@section('title', __('messages.edit_student'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_student') }}</h1><p class="page-sub">{{ $student->name }}</p></div>
    <a href="{{ route('admin.students.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-3">

    {{-- Main info --}}
    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.student_info') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الرقم الوطني</label>
                        <input type="text" name="national_id" value="{{ old('national_id', $student->national_id) }}" class="form-control @error('national_id') is-invalid @enderror">
                        @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.email_label') }}</label>
                        <input type="email" name="email" value="{{ old('email', $student->email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.phone_label') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.new_password') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="{{ __('messages.leave_blank_password') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.gender_label') }}</label>
                        <select name="gender" class="form-select">
                            <option value="">{{ __('messages.select_option') }}</option>
                            <option value="male"   @selected(old('gender', $student->gender) === 'male')>{{ __('messages.male') }}</option>
                            <option value="female" @selected(old('gender', $student->gender) === 'female')>{{ __('messages.female') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.nationality') }}</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $student->nationality) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.class_label') }}</label>
                        <select name="class_id" class="form-select">
                            <option value="">— {{ __('messages.select_class') }} —</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id', $student->class_id) == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        @if($student->avatar)
                        <img src="{{ asset('assets/uploads/students/'.$student->avatar) }}" class="rounded-circle mb-2" style="width:60px;height:60px;object-fit:cover">
                        @endif
                        <label class="form-label d-block">{{ __('messages.avatar_label') }}</label>
                        <input type="file" name="avatar" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $student->is_active))>
                            <label class="form-check-label" for="is_active">{{ __('messages.active_account') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Siblings panel --}}
    <div class="col-12 col-xl-4">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">
                    <i class="bi bi-people"></i> الإخوة
                    <span id="sib-count" class="pill pill-neutral ms-2" style="font-size:.72rem">
                        {{ count($siblingIds) }}
                    </span>
                </h2>
            </div>
            <div class="panel-card-body">
                <p class="text-muted" style="font-size:.82rem">اختر الطلاب الإخوة لهذا الطالب. سيظهر لهم زر التبديل السريع في التطبيق.</p>

                {{-- Search box --}}
                <input type="text" id="sib-search" class="form-control form-control-sm mb-2"
                       placeholder="ابحث باسم الطالب...">

                {{-- Scrollable list --}}
                <div id="sib-list" style="max-height:340px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:6px">
                    @forelse($allStudents as $s)
                    <label class="d-flex align-items-center gap-2 px-2 py-1 rounded sib-item"
                           style="cursor:pointer;transition:background .15s"
                           data-name="{{ mb_strtolower($s->name) }}">
                        <input type="checkbox"
                               name="siblings[]"
                               value="{{ $s->id }}"
                               class="form-check-input m-0 sib-cb"
                               {{ in_array($s->id, old('siblings', $siblingIds)) ? 'checked' : '' }}>
                        <span style="font-size:.88rem;line-height:1.3">{{ $s->name }}</span>
                    </label>
                    @empty
                    <p class="text-center py-3" style="color:var(--muted);font-size:.82rem">لا يوجد طلاب آخرون.</p>
                    @endforelse
                </div>

                {{-- Selected count --}}
                <div class="mt-2 text-end" style="font-size:.78rem;color:var(--muted)" id="sib-selected-label"></div>
            </div>
        </div>
    </div>

    {{-- Save button --}}
    <div class="col-12">
        <button type="submit" class="btn-primary-sm">
            <i class="bi bi-save"></i> {{ __('messages.save_changes') }}
        </button>
    </div>

</div>
</form>

@push('scripts')
<script>
(function () {
    const search  = document.getElementById('sib-search');
    const items   = document.querySelectorAll('.sib-item');
    const cbs     = document.querySelectorAll('.sib-cb');
    const counter = document.getElementById('sib-count');
    const label   = document.getElementById('sib-selected-label');

    function updateCount() {
        const n = document.querySelectorAll('.sib-cb:checked').length;
        counter.textContent = n;
        label.textContent   = n ? `${n} طالب محدد` : '';
    }

    search.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        items.forEach(function (el) {
            el.style.display = (!q || el.dataset.name.includes(q)) ? '' : 'none';
        });
    });

    cbs.forEach(function (cb) {
        cb.addEventListener('change', updateCount);
        // Hover highlight
        cb.closest('label').addEventListener('mouseenter', function () {
            this.style.background = '#f5f3ff';
        });
        cb.closest('label').addEventListener('mouseleave', function () {
            this.style.background = '';
        });
    });

    updateCount();
})();
</script>
@endpush

@endsection
