@extends('admin.layouts.app')
@section('title', __('messages.site_settings'))

@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.site_settings') }}</h1>
        <p class="page-sub">{{ __('messages.site_settings_sub') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
@endif

@php
function sv($settings, $key, $col = 'value_ar') {
    $item = $settings->get($key);

    return old(
        "{$key}_" . ($col === 'value_ar' ? 'ar' : 'en'),
        $item ? $item->{$col} : ''
    );
}
@endphp

{{-- App Store price visibility toggle --}}
@php $showPrice = \App\Models\SiteSetting::raw('show_price') ?: '1'; @endphp
<div class="panel-card mb-4">
    <div class="panel-card-header">
        <h2 class="panel-card-title"><i class="bi bi-phone me-2"></i>إعدادات تطبيق الجوال</h2>
    </div>
    <div class="panel-card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-semibold">إظهار / إخفاء السعر في App Store</div>
                <small class="text-muted">التطبيق سيُخفي أسعار الدورات عند إيقاف هذا الخيار</small>
            </div>
            <form action="{{ route('admin.site-settings.toggle-price') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-{{ $showPrice === '1' ? 'success' : 'secondary' }} d-flex align-items-center gap-2">
                    <i class="bi bi-{{ $showPrice === '1' ? 'eye' : 'eye-slash' }}"></i>
                    {{ $showPrice === '1' ? 'مفعّل — السعر ظاهر' : 'معطّل — السعر مخفي' }}
                </button>
            </form>
        </div>
    </div>
</div>

<form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- TABS NAV --}}
    <ul class="nav nav-tabs mb-4" id="settingsTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-hero"><i class="bi bi-image"></i> {{ __('messages.sett_tab_hero') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-about"><i class="bi bi-info-circle"></i> {{ __('messages.sett_tab_about') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-contact"><i class="bi bi-telephone"></i> {{ __('messages.sett_tab_contact') }}</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-social"><i class="bi bi-share"></i> {{ __('messages.sett_tab_social') }}</a></li>
    </ul>

    <div class="tab-content">

        {{-- ═══════════════════════════════════════════════════ HERO --}}
        <div class="tab-pane fade show active" id="tab-hero">
            <div class="panel-card">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.sett_tab_hero') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_hero_badge') }} (AR)</label>
                            <input type="text" name="hero_badge_ar" class="form-control" value="{{ sv($settings,'hero_badge','value_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_hero_badge') }} (EN)</label>
                            <input type="text" name="hero_badge_en" class="form-control" value="{{ sv($settings,'hero_badge','value_en') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_hero_subtitle') }} (AR)</label>
                            <textarea name="hero_subtitle_ar" class="form-control" rows="4">{{ sv($settings,'hero_subtitle','value_ar') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_hero_subtitle') }} (EN)</label>
                            <textarea name="hero_subtitle_en" class="form-control" rows="4">{{ sv($settings,'hero_subtitle','value_en') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_hero_image') }}</label>
                            <input type="file" name="hero_image" class="form-control" accept="image/*">
                            @if($settings->get('hero_image')?->value_ar)
                                <div class="mt-2">
                                    <img src="{{ asset('assets/uploads/site/'.$settings->get('hero_image')->value_ar) }}"
                                         style="height:80px;border-radius:6px;object-fit:cover">
                                </div>
                            @endif
                            <small class="text-muted">{{ __('messages.leave_empty_keep_file') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ ABOUT --}}
        <div class="tab-pane fade" id="tab-about">
            <div class="panel-card mb-3">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.sett_about_main') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_about_title') }} (AR)</label>
                            <input type="text" name="about_title_ar" class="form-control" value="{{ sv($settings,'about_title','value_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_about_title') }} (EN)</label>
                            <input type="text" name="about_title_en" class="form-control" value="{{ sv($settings,'about_title','value_en') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_about_desc') }} (AR)</label>
                            <textarea name="about_description_ar" class="form-control" rows="5">{{ sv($settings,'about_description','value_ar') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_about_desc') }} (EN)</label>
                            <textarea name="about_description_en" class="form-control" rows="5">{{ sv($settings,'about_description','value_en') }}</textarea>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('messages.sett_about_years') }}</label>
                            <input type="number" name="about_years" class="form-control" value="{{ $settings->get('about_years')?->value_ar ?? '25' }}" min="0">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ __('messages.sett_about_img_main') }}</label>
                            <input type="file" name="about_image_main" class="form-control" accept="image/*">
                            @if($settings->get('about_image_main')?->value_ar)
                                <img src="{{ asset('assets/uploads/site/'.$settings->get('about_image_main')->value_ar) }}"
                                     class="mt-2" style="height:80px;border-radius:6px;object-fit:cover">
                            @endif
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ __('messages.sett_about_img_sec') }}</label>
                            <input type="file" name="about_image_secondary" class="form-control" accept="image/*">
                            @if($settings->get('about_image_secondary')?->value_ar)
                                <img src="{{ asset('assets/uploads/site/'.$settings->get('about_image_secondary')->value_ar) }}"
                                     class="mt-2" style="height:80px;border-radius:6px;object-fit:cover">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Values --}}
            <div class="panel-card">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.sett_about_values') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        @foreach([1,2,3,4] as $i)
                        <div class="col-12">
                            <div class="p-3 rounded" style="background:var(--bg-light,#f8f9fa);border:1px solid var(--border)">
                                <p class="fw-semibold mb-2">{{ __('messages.sett_value') }} {{ $i }}</p>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm">{{ __('messages.sett_value_title') }} (AR)</label>
                                        <input type="text" name="about_value{{ $i }}_title_ar" class="form-control form-control-sm"
                                               value="{{ sv($settings,'about_value'.$i.'_title','value_ar') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm">{{ __('messages.sett_value_title') }} (EN)</label>
                                        <input type="text" name="about_value{{ $i }}_title_en" class="form-control form-control-sm"
                                               value="{{ sv($settings,'about_value'.$i.'_title','value_en') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm">{{ __('messages.sett_value_desc') }} (AR)</label>
                                        <input type="text" name="about_value{{ $i }}_desc_ar" class="form-control form-control-sm"
                                               value="{{ sv($settings,'about_value'.$i.'_desc','value_ar') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm">{{ __('messages.sett_value_desc') }} (EN)</label>
                                        <input type="text" name="about_value{{ $i }}_desc_en" class="form-control form-control-sm"
                                               value="{{ sv($settings,'about_value'.$i.'_desc','value_en') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ CONTACT --}}
        <div class="tab-pane fade" id="tab-contact">
            <div class="panel-card">
                <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.sett_tab_contact') }}</h2></div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_contact_address') }} (AR)</label>
                            <input type="text" name="contact_address_ar" class="form-control" value="{{ sv($settings,'contact_address','value_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_contact_address') }} (EN)</label>
                            <input type="text" name="contact_address_en" class="form-control" value="{{ sv($settings,'contact_address','value_en') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.sett_contact_phone') }}</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ $settings->get('contact_phone')?->value_ar ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.sett_contact_whatsapp') }}</label>
                            <input type="text" name="contact_whatsapp" class="form-control"
                                   placeholder="+962791234567"
                                   value="{{ $settings->get('contact_whatsapp')?->value_ar ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.sett_contact_email') }}</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ $settings->get('contact_email')?->value_ar ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_contact_hours') }} (AR)</label>
                            <input type="text" name="contact_hours_ar" class="form-control" value="{{ sv($settings,'contact_hours','value_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.sett_contact_hours') }} (EN)</label>
                            <input type="text" name="contact_hours_en" class="form-control" value="{{ sv($settings,'contact_hours','value_en') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ SOCIAL & APPS --}}
        <div class="tab-pane fade" id="tab-social">
            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <div class="panel-card">
                        <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.sett_social_title') }}</h2></div>
                        <div class="panel-card-body">
                            <div class="row g-3">
                                @foreach([
                                    ['social_facebook',  'bi-facebook',  'Facebook'],
                                    ['social_instagram', 'bi-instagram', 'Instagram'],
                                    ['social_youtube',   'bi-youtube',   'YouTube'],
                                    ['social_twitter',   'bi-twitter-x', 'Twitter / X'],
                                    ['social_tiktok',    'bi-tiktok',    'TikTok'],
                                    ['social_snapchat',  'bi-snapchat',  'Snapchat'],
                                    ['social_whatsapp',  'bi-whatsapp',  'WhatsApp'],
                                ] as [$key, $icon, $label])
                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi {{ $icon }}"></i> {{ $label }}
                                    </label>
                                    <input type="url" name="{{ $key }}" class="form-control"
                                           placeholder="https://..."
                                           value="{{ $settings->get($key)?->value_ar ?? '' }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="panel-card">
                        <div class="panel-card-header"><h2 class="panel-card-title">{{ __('messages.sett_apps_title') }}</h2></div>
                        <div class="panel-card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label"><i class="bi bi-google-play"></i> Google Play</label>
                                    <input type="url" name="app_google_play" class="form-control"
                                           placeholder="https://play.google.com/store/apps/..."
                                           value="{{ $settings->get('app_google_play')?->value_ar ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><i class="bi bi-apple"></i> App Store</label>
                                    <input type="url" name="app_store" class="form-control"
                                           placeholder="https://apps.apple.com/..."
                                           value="{{ $settings->get('app_store')?->value_ar ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end tab-content --}}

    <div class="mt-4">
        <button type="submit" class="btn-primary-sm" style="padding:12px 32px">
            <i class="bi bi-save"></i> {{ __('messages.save_changes') }}
        </button>
    </div>
</form>

@endsection
