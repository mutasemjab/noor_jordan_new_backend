@extends('admin.layouts.app')
@section('title', 'تعديل بانر')

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">تعديل بانر</h1></div>
    <a href="{{ route('admin.banners.index') }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> رجوع
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-7">
<form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">بيانات البانر</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">

            <div class="col-12">
                <label class="form-label">الصورة الحالية</label><br>
                <img src="{{ asset('assets/uploads/banners/' . $banner->image) }}"
                     id="previewImg"
                     style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0">
            </div>

            <div class="col-12">
                <label class="form-label">تغيير الصورة (اتركه فارغاً للإبقاء على الحالية)</label>
                <input type="file" name="image" accept="image/*"
                       class="form-control @error('image') is-invalid @enderror"
                       onchange="previewNew(this)">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">الأبعاد الموصى بها: 1200 × 450 بكسل — JPG, PNG, WEBP — بحد أقصى 4MB</small>
            </div>

            <div class="col-md-6">
                <label class="form-label">الترتيب</label>
                <input type="number" name="order_index"
                       value="{{ old('order_index', $banner->order_index) }}"
                       min="0" class="form-control">
                <small class="text-muted">الأصغر يظهر أولاً</small>
            </div>

            <div class="col-md-6 d-flex align-items-center">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" @checked(old('is_active', $banner->is_active))>
                    <label class="form-check-label" for="is_active">نشط</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-primary-sm">
                    <i class="bi bi-save"></i> حفظ التغييرات
                </button>
            </div>

        </div>
    </div>
</div>
</form>
</div>
</div>

@endsection

@push('scripts')
<script>
function previewNew(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('previewImg').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
