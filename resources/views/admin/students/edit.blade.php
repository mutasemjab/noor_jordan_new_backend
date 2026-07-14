@extends('admin.layouts.app')
@section('title', __('messages.edit_student'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div><h1 class="page-title">{{ __('messages.edit_student') }}</h1><p class="page-sub">{{ $student->name }}</p></div>
    <a href="{{ route('admin.students.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left"></i> {{ __('messages.Back') }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
<div class="col-12 col-xl-8">
<form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="panel-card">
    <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.student_info') }}</h2></div>
    <div class="panel-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">الرقم الوطني</label>
                <input type="text" name="national_id" value="{{ old('national_id', $student->national_id) }}" class="form-control @error('national_id') is-invalid @enderror" placeholder="مثال: 9876543210">
                @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.email_label') }}</label>
                <input type="email" name="email" value="{{ old('email', $student->email) }}" class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.new_password') }}</label>
                <input type="password" name="password" class="form-control" placeholder="{{ __('messages.leave_blank_password') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.phone_label') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.gender_label') }}</label>
                <select name="gender" class="form-select">
                    <option value="">{{ __('messages.select_option') }}</option>
                    <option value="male" @selected(old('gender', $student->gender) === 'male')>{{ __('messages.male') }}</option>
                    <option value="female" @selected(old('gender', $student->gender) === 'female')>{{ __('messages.female') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.nationality') }}</label>
                <input type="text" name="nationality" value="{{ old('nationality', $student->nationality) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.class_label') }}</label>
                <select name="class_id" class="form-select">
                    <option value="">— {{ __('messages.select_class') }} —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id', $student->class_id) == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $student->is_active))>
                    <label class="form-check-label" for="is_active">{{ __('messages.active_account') }}</label>
                </div>
            </div>
            <div class="col-md-6">
                @if($student->avatar)
                <img src="{{ asset('assets/uploads/students/'.$student->avatar) }}" class="rounded-circle mb-2" style="width:60px;height:60px;object-fit:cover">
                @endif
                <label class="form-label d-block">{{ __('messages.avatar_label') }}</label>
                <input type="file" name="avatar" accept="image/*" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('messages.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>
</form>
</div>

<div class="col-12 col-xl-4">
    <div class="panel-card">
        <div class="panel-card-header"><h2 class="panel-card-title">معلومات الجهاز</h2></div>
        <div class="panel-card-body">
            @if($student->deviceId)
                <p class="text-muted mb-1" style="font-size:13px">الجهاز المسجّل:</p>
                <code class="d-block mb-3" style="font-size:11px;word-break:break-all">{{ $student->deviceId }}</code>
                <form action="{{ route('admin.students.reset-device', $student->id) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد؟ سيتمكن الطالب من تسجيل الدخول من أي جهاز.')">
                    @csrf
                    <button type="submit" class="btn-danger-sm w-100">
                        <i class="bi bi-phone-vibrate"></i> إعادة تعيين الجهاز
                    </button>
                </form>
            @else
                <p class="text-muted mb-0" style="font-size:13px">
                    <i class="bi bi-phone-x"></i> لم يُسجَّل جهاز بعد
                </p>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
