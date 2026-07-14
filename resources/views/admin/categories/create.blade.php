@extends('admin.layouts.app')
@section('title', __('messages.add_category'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.add_category') }}</h1>
        @if($parent)
            <p class="page-sub">
                {{ __('messages.under') }}: <strong>{{ $parent->name_ar }}</strong>
                <span class="text-muted">({{ $parent->name_en }})</span>
            </p>
        @endif
    </div>
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
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- Hidden parent_id --}}
<input type="hidden" name="parent_id" value="{{ old('parent_id', $parent?->id) }}">

<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.category_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">

            {{-- Parent picker (only shown when adding root, or to change parent) --}}
            <div class="col-12">
                <label class="form-label">{{ __('messages.parent_category') }}</label>
                <select name="parent_id" class="form-select">
                    <option value="">— {{ __('messages.root_category') }} —</option>
                    @foreach($allCats as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('parent_id', $parent?->id) == $cat->id ? 'selected' : '' }}>
                            {{ str_repeat('— ', $cat->level) }}{{ $cat->name_ar }} ({{ $cat->name_en }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                       class="form-control @error('name_ar') is-invalid @enderror" dir="rtl" required>
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en') }}"
                       class="form-control @error('name_en') is-invalid @enderror" required>
                @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-8">
                <label class="form-label">{{ __('messages.icon_bi') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i id="iconPreview" class="bi bi-tag"></i></span>
                    <input type="text" name="icon" value="{{ old('icon') }}" class="form-control"
                           placeholder="bi-book" oninput="document.getElementById('iconPreview').className='bi '+this.value">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.order') }}</label>
                <input type="number" name="order_index" value="{{ old('order_index', 0) }}" min="0" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label">{{ __('messages.image_label') }}</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('messages.Active') }}</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-primary-sm">
                    <i class="bi bi-save"></i> {{ __('messages.save_category') }}
                </button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>
@endsection
