@extends('admin.layouts.app')
@section('title', __('messages.edit_pos'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_pos') }}</h1></div>
    <a href="{{ route('admin.pos.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-7">
<form action="{{ route('admin.pos.update', $po->id) }}" method="POST">
@csrf @method('PUT')
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.pos_details') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_ar') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar', $po->name_ar) }}"
                       class="form-control @error('name_ar') is-invalid @enderror" dir="rtl" required>
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en', $po->name_en) }}"
                       class="form-control @error('name_en') is-invalid @enderror" required>
                @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.city_label') }} <span class="text-danger">*</span></label>
                <select name="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_city_ph') }}</option>
                    @foreach($cities as $city)
                    <option value="{{ $city->id }}" @selected(old('city_id', $po->city_id) == $city->id)>
                        {{ $city->name_en }} ({{ $city->name_ar }})
                    </option>
                    @endforeach
                </select>
                @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.phone_label') }} <span class="text-danger">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $po->phone) }}"
                       class="form-control @error('phone') is-invalid @enderror" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.google_maps_link') }}</label>
                <input type="url" name="google_map_link" value="{{ old('google_map_link', $po->google_map_link) }}"
                       class="form-control @error('google_map_link') is-invalid @enderror"
                       placeholder="https://maps.google.com/...">
                @error('google_map_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>

@endsection
