@extends('admin.layouts.app')
@section('title', __('messages.edit_category'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_category') }}: {{ $category->name_ar }}</h1></div>
    <a href="{{ route('admin.categories.index') }}" class="btn-outline-sm">
        <i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-7">
<form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.category_details') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">

            {{-- Parent picker --}}
            <div class="col-12">
                <label class="form-label">{{ __('messages.parent_category') }}</label>
                <select name="parent_id" class="form-select">
                    <option value="">— {{ __('messages.root_category') }} —</option>
                    @foreach($allCats as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                            {{ str_repeat('— ', $cat->level) }}{{ $cat->name_ar }} ({{ $cat->name_en }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar', $category->name_ar) }}"
                       class="form-control @error('name_ar') is-invalid @enderror" dir="rtl" required>
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en', $category->name_en) }}"
                       class="form-control @error('name_en') is-invalid @enderror" required>
                @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-8">
                <label class="form-label">{{ __('messages.icon_bi') }}</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i id="iconPreview" class="bi {{ $category->icon ?? 'bi-tag' }}"></i>
                    </span>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                           class="form-control" placeholder="bi-book"
                           oninput="document.getElementById('iconPreview').className='bi '+this.value">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.order') }}</label>
                <input type="number" name="order_index" value="{{ old('order_index', $category->order_index) }}"
                       min="0" class="form-control">
            </div>

            <div class="col-12">
                @if($category->image)
                    <img src="{{ asset('assets/uploads/categories/' . $category->image) }}"
                         class="mb-2 rounded" style="width:80px;height:80px;object-fit:cover">
                @endif
                <label class="form-label d-block">{{ __('messages.category_image') }}</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" @checked(old('is_active', $category->is_active))>
                    <label class="form-check-label" for="is_active">{{ __('messages.Active') }}</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-primary-sm">
                    <i class="bi bi-save"></i> {{ __('messages.save_changes') }}
                </button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>
@endsection
