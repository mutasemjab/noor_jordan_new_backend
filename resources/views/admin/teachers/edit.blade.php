@extends('admin.layouts.app')
@section('title', __('messages.edit_teacher'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_teacher') }}</h1><p class="page-sub">{{ $teacher->name }}</p></div>
    <a href="{{ route('admin.teachers.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-3">

    <div class="col-12 col-xl-8">
        <div class="panel-card">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.teacher_info') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.email_label') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.new_password') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="{{ __('messages.leave_blank_password') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.phone_label') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.gender_label') }}</label>
                        <select name="gender" class="form-select">
                            <option value="">{{ __('messages.select_option') }}</option>
                            <option value="male" @selected(old('gender', $teacher->gender) === 'male')>{{ __('messages.male') }}</option>
                            <option value="female" @selected(old('gender', $teacher->gender) === 'female')>{{ __('messages.female') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.nationality') }}</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $teacher->nationality) }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.settings') }}</h2></div>
            <div class="panel-card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('messages.Active') }}</label>
                </div>
                <hr>
                @if($teacher->avatar)
                    <img src="{{ asset('assets/uploads/teachers/'.$teacher->avatar) }}" class="img-fluid rounded mb-2" alt="">
                @endif
                <label class="form-label">{{ __('messages.profile_photo') }}</label>
                <input type="file" name="avatar" accept="image/*" class="form-control mb-3">
                <button type="submit" class="btn-primary-sm w-100 justify-content-center">
                    <i class="bi bi-save"></i> {{ __('messages.update_teacher') }}
                </button>
            </div>
        </div>
    </div>

</div>
</form>
@endsection
