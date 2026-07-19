@extends('admin.layouts.app')
@section('title', 'إضافة صف جديد')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">إضافة صف جديد</h1>
    </div>
    <a href="{{ route('admin.classes.index') }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> رجوع
    </a>
</div>

<div class="row g-3">
    <div class="col-12 col-md-6">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">بيانات الصف</h2></div>
            <div class="panel-card-body">
                <form action="{{ route('admin.classes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم الصف <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="مثال: الصف الأول — شعبة أ" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مربي الصف</label>
                        <select name="homeroom_teacher_id" class="form-select select2 @error('homeroom_teacher_id') is-invalid @enderror">
                            <option value="">— اختر المعلم —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id') == $teacher->id)>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('homeroom_teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">الصف نشط</label>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                        <i class="bi bi-save"></i> حفظ الصف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
