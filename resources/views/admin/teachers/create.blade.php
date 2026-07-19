@extends('admin.layouts.app')
@section('title', __('messages.add_teacher'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.add_teacher') }}</h1></div>
    <a href="{{ route('admin.teachers.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-3">

    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.teacher_info') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                         <div class="col-md-6">
                <label class="form-label">الرقم الوطني</label>
                <input type="text" name="national_id" value="{{ old('national_id') }}" class="form-control @error('national_id') is-invalid @enderror" placeholder="مثال: 9876543210">
                @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.email_label') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.password_label') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.confirm_password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.phone_label') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.gender_label') }}</label>
                        <select name="gender" class="form-select select2">
                            <option value="">{{ __('messages.select_option') }}</option>
                            <option value="male" @selected(old('gender') === 'male')>{{ __('messages.male') }}</option>
                            <option value="female" @selected(old('gender') === 'female')>{{ __('messages.female') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.nationality') }}</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.settings') }}</h2></div>
            <div class="panel-card-body">
                <label class="form-label">{{ __('messages.profile_photo') }}</label>
                <input type="file" name="avatar" accept="image/*" class="form-control mb-3">
                <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                    <i class="bi bi-save"></i> {{ __('messages.save_teacher') }}
                </button>
            </div>
        </div>
    </div>

</div>
</form>
@endsection
