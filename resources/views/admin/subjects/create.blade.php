@extends('admin.layouts.app')
@section('title', __('messages.add_subject'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.add_subject') }}</h1>
        @if($parentPreset)
            <p class="page-sub">{{ __('messages.under') }}: <strong>{{ $parentPreset->name_ar }}</strong></p>
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
<form action="{{ route('admin.subjects.store') }}" method="POST">
@csrf
<input type="hidden" name="redirect_to_tree" value="{{ request('redirect_to_tree', 0) }}">

<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.subject_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">

            {{-- Category tree picker --}}
            <div class="col-12">
                <label class="form-label">{{ __('messages.category') }} <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">— {{ __('messages.select_option') }} —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $parentPreset?->id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->full_path }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">{{ __('messages.subject_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                       class="form-control @error('name_ar') is-invalid @enderror" dir="rtl" required>
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.subject_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en') }}"
                       class="form-control @error('name_en') is-invalid @enderror" required>
                @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.icon_class') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i id="subIconPreview" class="bi bi-journal-bookmark"></i></span>
                    <input type="text" name="icon" value="{{ old('icon') }}" class="form-control"
                           placeholder="bi-book"
                           oninput="document.getElementById('subIconPreview').className='bi '+this.value">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.color_class') }}</label>
                <input type="text" name="color_class" value="{{ old('color_class') }}" class="form-control"
                       placeholder="text-primary">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.order_index') }}</label>
                <input type="number" name="order_index" value="{{ old('order_index', 0) }}" min="0" class="form-control">
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('messages.Active') }}</label>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn-primary-sm">
                    <i class="bi bi-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>
@endsection
