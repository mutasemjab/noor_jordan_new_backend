@extends('admin.layouts.app')
@section('title', 'إضافة إعلان')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">إضافة إعلان جديد</h1></div>
    <a href="{{ route('admin.announcements.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> رجوع</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-8">
<form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">بيانات الإعلان</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">المحتوى <span class="text-danger">*</span></label>
                <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">الصف المستهدف (اتركه فارغاً للجميع)</label>
                <select name="class_id" class="form-select">
                    <option value="">— للجميع —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">تاريخ النشر (اتركه فارغاً للنشر الآن)</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">صورة (اختياري)</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true))>
                    <label class="form-check-label" for="is_active">نشر فوراً</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> حفظ الإعلان</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@endsection
