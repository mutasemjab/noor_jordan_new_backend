@extends('admin.layouts.app')
@section('title', 'إضافة باص')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">إضافة باص جديد</h1></div>
    <a href="{{ route('admin.buses.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> رجوع</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-6">
<form action="{{ route('admin.buses.store') }}" method="POST">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">بيانات الباص</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">اسم الباص <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror" placeholder="مثال: باص 1" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">السعة (عدد المقاعد) <span class="text-danger">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', 25) }}" min="1" max="200"
                       class="form-control @error('capacity') is-invalid @enderror" required>
                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">نشط</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> حفظ</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>
@endsection
