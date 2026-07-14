@extends('admin.layouts.app')
@section('title', 'تعديل إعلان')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">تعديل إعلان</h1><p class="page-sub">{{ $announcement->title }}</p></div>
    <a href="{{ route('admin.announcements.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> رجوع</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-8">
<form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">بيانات الإعلان</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">المحتوى <span class="text-danger">*</span></label>
                <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $announcement->body) }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">الصف المستهدف</label>
                <select name="class_id" class="form-select">
                    <option value="">— للجميع —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id', $announcement->class_id) == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">تاريخ النشر</label>
                <input type="datetime-local" name="published_at"
                       value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}"
                       class="form-control">
            </div>
            @if($announcement->image)
            <div class="col-12">
                <label class="form-label">الصورة الحالية</label><br>
                <img src="{{ asset('assets/uploads/' . $announcement->image) }}" style="max-height:120px;border-radius:8px" class="mb-2">
            </div>
            @endif
            <div class="col-12">
                <label class="form-label">{{ $announcement->image ? 'تغيير الصورة' : 'إضافة صورة (اختياري)' }}</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', $announcement->is_active))>
                    <label class="form-check-label" for="is_active">نشط</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> حفظ التغييرات</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@endsection
