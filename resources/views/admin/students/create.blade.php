@extends('admin.layouts.app')
@section('title', __('messages.add_student'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.add_student') }}</h1></div>
    <a href="{{ route('admin.students.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-8">
<form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.student_info') }}</h2></div>
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
                <label class="form-label">{{ __('messages.email_label') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
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
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.phone_label') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.gender_label') }}</label>
                <select name="gender" class="form-select">
                    <option value="">{{ __('messages.select_option') }}</option>
                    <option value="male" @selected(old('gender') === 'male')>{{ __('messages.male') }}</option>
                    <option value="female" @selected(old('gender') === 'female')>{{ __('messages.female') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.nationality') }}</label>
                <input type="text" name="nationality" value="{{ old('nationality') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.class_label') }}</label>
                <select name="class_id" class="form-select">
                    <option value="">— {{ __('messages.select_class') }} —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.avatar_label') }}</label>
                <input type="file" name="avatar" accept="image/*" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.create_student') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>
</div>
@endsection
