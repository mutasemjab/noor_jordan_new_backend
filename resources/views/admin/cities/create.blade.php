@extends('admin.layouts.app')
@section('title', __('messages.add_city'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.add_city') }}</h1></div>
    <a href="{{ route('admin.cities.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-6">
<form action="{{ route('admin.cities.store') }}" method="POST">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.city_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
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
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.save_city') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@endsection
