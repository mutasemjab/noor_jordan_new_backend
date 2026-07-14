@extends('teacher.layouts.app')
@section('title', __('messages.t_my_profile'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.t_my_profile') }}</h1>
        <p class="page-sub">{{ __('messages.t_profile_sub') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-3">

    <div class="col-12 col-xl-8">
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_personal_info') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_new_password') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="{{ __('messages.t_leave_blank_password') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_professional_info') }}</h2></div>
            <div class="panel-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_specialization_ar') }}</label>
                        <input type="text" name="specialization_ar" value="{{ old('specialization_ar', $teacher->specialization_ar) }}" class="form-control" dir="rtl">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_specialization_en') }}</label>
                        <input type="text" name="specialization_en" value="{{ old('specialization_en', $teacher->specialization_en) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_qualification_ar') }}</label>
                        <input type="text" name="qualification_ar" value="{{ old('qualification_ar', $teacher->qualification_ar) }}" class="form-control" dir="rtl">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_qualification_en') }}</label>
                        <input type="text" name="qualification_en" value="{{ old('qualification_en', $teacher->qualification_en) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_bio_ar') }}</label>
                        <textarea name="bio_ar" rows="3" class="form-control" dir="rtl">{{ old('bio_ar', $teacher->bio_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.t_bio_en') }}</label>
                        <textarea name="bio_en" rows="3" class="form-control">{{ old('bio_en', $teacher->bio_en) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.t_years_of_experience') }}</label>
                        <input type="number" name="years_of_experience" value="{{ old('years_of_experience', $teacher->years_of_experience) }}" min="0" class="form-control">
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_profile_photo') }}</h2></div>
            <div class="panel-card-body text-center">
                @if($teacher->avatar)
                    <img src="{{ asset('uploads/teachers/'.$teacher->avatar) }}" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:120px;height:120px;background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                        <span style="color:#fff;font-size:2.5rem;font-weight:700">{{ strtoupper(substr($teacher->name, 0, 1)) }}</span>
                    </div>
                @endif
                <input type="file" name="avatar" accept="image/*" class="form-control">
            </div>
        </div>

        <div class="panel-card mb-3">
            <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.t_stats') }}</h2></div>
            <div class="panel-card-body">
                <div style="font-size:.85rem">
                    <div class="d-flex justify-content-between mb-2"><span style="color:var(--muted)">{{ __('messages.t_total_courses') }}</span><strong>{{ $teacher->total_courses ?? 0 }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span style="color:var(--muted)">{{ __('messages.t_total_students') }}</span><strong>{{ $teacher->total_students ?? 0 }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span style="color:var(--muted)">{{ __('messages.t_avg_rating') }}</span><strong>{{ number_format($teacher->average_rating ?? 0, 1) }} ★</strong></div>
                    <div class="d-flex justify-content-between"><span style="color:var(--muted)">{{ __('messages.t_verified') }}</span><strong>{{ $teacher->is_verified ? '✓ ' . __('messages.t_yes') : __('messages.t_pending') }}</strong></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary-sm w-100 justify-content-center">
            <i class="bi bi-save"></i> {{ __('messages.t_save_profile') }}
        </button>
    </div>

</div>
</form>
@endsection
