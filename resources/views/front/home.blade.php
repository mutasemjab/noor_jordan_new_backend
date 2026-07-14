@extends('layouts.front')
@section('title', __('front.page_title'))

@section('content')
<section id="hero">
  <div class="hero-bg"></div>
  <div class="hero-img-overlay"></div>
  <canvas id="particles-canvas"></canvas>
  <div class="hero-slash"></div>

  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge">{{ sett('hero_badge') ?: __('front.site_tagline') }}</div>
      <h1 class="hero-title" id="hero-title">
        {{ __('front.site_name') }}<br>
        <span class="accent-blue">{{ app()->getLocale() === 'ar' ? 'الباحث' : 'Al-Bahith' }}</span>
        <span style="color:rgba(255,255,255,0.3)"> · </span>
        <span class="accent-red">{{ app()->getLocale() === 'ar' ? 'التميّز' : 'Excellence' }}</span>
      </h1>
      <p class="hero-sub">{{ sett('hero_subtitle') ?: __('front.hero_sub') }}</p>
      <div class="hero-actions">
        <a href="#courses" class="btn-primary">
          <span>{{ __('front.hero_cta_start') }}</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#about" class="btn-secondary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8" fill="currentColor" stroke="none"/></svg>
          <span>{{ __('front.hero_cta_about') }}</span>
        </a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-number" data-count="{{ $stats['students'] }}"><span>+</span>0</div>
          <div class="stat-label">{{ __('front.hero_stat_reg') }}</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-count="{{ $stats['teachers'] }}">0</div>
          <div class="stat-label">{{ __('front.hero_stat_teachers') }}</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-count="{{ $stats['satisfaction'] }}"><span>%</span>0</div>
          <div class="stat-label">{{ __('front.hero_stat_success') }}</div>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-card-main reveal-right">
        @php $heroImg = sett_raw('hero_image'); @endphp
        <img src="{{ $heroImg ? asset('assets/uploads/site/'.$heroImg) : 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=600&q=80&auto=format' }}" alt="Students">
        <div class="hero-card-main-body">
          <div class="hero-card-badge">{{ __('front.hero_card_year') }}</div>
          <h4>{{ __('front.hero_card_path') }}</h4>
          <p>{{ __('front.hero_card_desc') }}</p>
        </div>
      </div>
      <div class="card-float-1">
        <div class="f-num">⭐ 4.9</div>
        <div class="f-label">{{ __('front.hero_float_parents') }}</div>
      </div>
      <div class="card-float-2">
        <div class="f-num">🏆 #1</div>
        <div class="f-label">{{ __('front.hero_float_rank') }}</div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <div class="scroll-line"></div>
    <span>SCROLL</span>
  </div>
</section>

<!-- ======= STATS BAND ======= -->
<div class="stats-band">
  <div class="stats-band-inner">
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="{{ sett_raw('about_years') ?: 25 }}"><span>+</span>0</div>
      <div class="stat-band-label">{{ __('front.stats_years') }}</div>
    </div>
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="{{ $stats['students'] }}"><span>+</span>0</div>
      <div class="stat-band-label">{{ __('front.stats_trust') }}</div>
    </div>
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="{{ $stats['teachers'] }}">0</div>
      <div class="stat-band-label">{{ __('front.stats_staff') }}</div>
    </div>
    <div class="stat-band-item reveal">
      <div class="stat-band-num" data-count="{{ $stats['satisfaction'] }}"><span>%</span>0</div>
      <div class="stat-band-label">{{ __('front.stats_tawjihi') }}</div>
    </div>
  </div>
</div>

<!-- ======= GRADES ======= -->
<section id="grades">
  <div class="grades-inner">
    <div class="section-header reveal">
      <div class="section-eyebrow">{{ __('front.grades_tag') }}</div>
      <h2 class="section-title">{{ __('front.grades_title') }}<br><span class="text-gradient-blue">{{ __('front.grades_title_span') }}</span></h2>
      <p class="section-sub">{{ __('front.grades_desc') }}</p>
    </div>
    <div class="grades-grid">

      <div class="grade-card reveal-left" onclick="APP.open('grades')" style="cursor:pointer">
        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80&auto=format" alt="{{ __('front.grades_basic_tag') }}">
        <div class="grade-overlay"></div>
        <div class="grade-content">
          <div class="grade-tag tag-blue">{{ __('front.grades_basic_tag') }}</div>
          <h3 class="grade-title">{{ __('front.grades_basic_t1') }}<br>{{ __('front.grades_basic_t2') }}</h3>
          <p class="grade-desc">{{ __('front.grades_basic_desc') }}</p>
          <div class="grade-meta">
            <div class="grade-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              <span>{{ __('front.grades_basic_students') }}</span>
            </div>
            <div class="grade-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span>{{ __('front.grades_open') }}</span>
            </div>
            <div class="grade-arrow">→</div>
          </div>
        </div>
      </div>

      <div class="grade-card reveal-right" onclick="APP.open('tawjihi')" style="cursor:pointer">
        <img src="https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=800&q=80&auto=format" alt="{{ __('front.grades_tawjihi_tag') }}">
        <div class="grade-overlay"></div>
        <div class="grade-content">
          <div class="grade-tag tag-red">{{ __('front.grades_tawjihi_tag') }}</div>
          <h3 class="grade-title">{{ __('front.grades_tawjihi_t1') }}<br>{{ __('front.grades_tawjihi_t2') }}</h3>
          <p class="grade-desc">{{ __('front.grades_tawjihi_desc') }}</p>
          <div class="grade-meta">
            <div class="grade-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              <span>{{ __('front.grades_tawjihi_students') }}</span>
            </div>
            <div class="grade-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>{{ __('front.grades_success') }}</span>
            </div>
            <div class="grade-arrow">→</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ======= TEACHERS ======= -->
<section id="teachers">
  <div class="section">
    <div class="section-header reveal">
      <div class="section-eyebrow">{{ __('front.teachers_section_tag') }}</div>
      <h2 class="section-title">{{ __('front.teachers_section_t') }}<br><span class="text-gradient-red">{{ __('front.teachers_section_span') }}</span></h2>
      <p class="section-sub">{{ __('front.teachers_section_desc') }}</p>
    </div>
    <div class="teachers-grid">
      @forelse($teachers as $teacher)
     <a href="{{ route('teachers.show',$teacher->id) }}" style="text-decoration:none;color:inherit;display:block">
      <div class="teacher-card reveal">
        <div class="teacher-img-wrap">
          @if($teacher->avatar)
            <img src="{{ asset('assets/uploads/'.$teacher->avatar) }}" alt="{{ $teacher->name }}">
          @else
            <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=400&q=80&auto=format" alt="{{ $teacher->name }}">
          @endif
          <div class="teacher-subject-badge">{{ strtoupper(substr($teacher->name, 0, 4)) }}</div>
        </div>
        <div class="teacher-card-body">
          <div class="teacher-name">{{ $teacher->name }}</div>
          <div class="teacher-role">
            {{ $teacher->specialization }}
            @if($teacher->years_of_experience) — {{ $teacher->years_of_experience }} {{ __('front.teachers_experience') }}@endif
          </div>
          <div class="teacher-stats-row">
            <div class="t-stat">
              <div class="t-stat-num">{{ $teacher->total_students ?? 0 }}<span>+</span></div>
              <div class="t-stat-label">{{ __('front.teacher_stat_students') }}</div>
            </div>
            <div class="t-stat">
              <div class="t-stat-num">{{ number_format($teacher->average_rating ?? 4.8, 1) }}</div>
              <div class="t-stat-label">{{ __('front.teacher_stat_rating') }}</div>
            </div>
            <div class="t-stat">
              <div class="t-stat-num">97<span>%</span></div>
              <div class="t-stat-label">{{ __('front.teacher_stat_success') }}</div>
            </div>
          </div>
        </div>
      </div>
      </a>
      @empty
      <p class="text-muted">{{ __('front.courses_no_courses') }}</p>
      @endforelse
    </div>
  </div>
</section>

<!-- ======= COURSES ======= -->
<section id="courses">
  <div class="courses-inner">
    <div class="section-header reveal">
      <div class="section-eyebrow">{{ __('front.courses_section_tag') }}</div>
      <h2 class="section-title">{{ __('front.courses_section_t') }}<br><span class="text-gradient-blue">{{ __('front.courses_section_span') }}</span></h2>
      <p class="section-sub">{{ __('front.courses_section_desc') }}</p>
    </div>
    <div class="courses-grid">
      @forelse($courses as $course)
      <div class="course-card reveal">
        <div class="course-thumb">
          @if($course->thumbnail)
            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}">
          @else
            <img src="https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=600&q=80&auto=format" alt="{{ $course->title }}">
          @endif
          @php
            $levelKey = match($course->difficulty_level ?? '') {
              'advanced'     => 'course_level_advanced',
              'intermediate' => 'course_level_inter',
              default        => 'course_level_basic',
            };
            $levelClass = match($course->difficulty_level ?? '') {
              'advanced'     => 'level-advanced',
              'intermediate' => 'level-inter',
              default        => 'level-basic',
            };
          @endphp
          <span class="course-level-badge {{ $levelClass }}">{{ __('front.'.$levelKey) }}</span>
          @if($course->duration_hours)
            <span class="course-duration">⏱ {{ $course->duration_hours }} {{ __('front.course_hours_unit') }}</span>
          @endif
        </div>
        <div class="course-body">
          <div class="course-category">{{ app()->getLocale() === 'ar' ? ($course->category->name_ar ?? '') : ($course->category->name_en ?? $course->category->name_ar ?? '') }}</div>
          <h3 class="course-title">{{ $course->title }}</h3>
          <p class="course-desc">{{ $course->description }}</p>
          <div class="course-meta">
            <div class="course-rating">
              <span class="stars">{{ str_repeat('★', (int)round($course->average_rating ?? 5)) }}{{ str_repeat('☆', 5 - (int)round($course->average_rating ?? 5)) }}</span>
              <span class="rating-num">{{ number_format($course->average_rating ?? 4.9, 1) }}</span>
            </div>
            <div class="course-meta-item">👥 {{ $course->total_students ?? 0 }} {{ __('front.teacher_stat_students') }}</div>
          </div>
          <a href="{{ route('courses.show', $course->id) }}" class="course-enroll-btn">{{ __('front.courses_enroll') }}</a>
        </div>
      </div>
      @empty
      <p class="text-muted">{{ __('front.courses_no_courses') }}</p>
      @endforelse
    </div>
  </div>
</section>

<!-- ======= ABOUT ======= -->
<section id="about">
  <div class="about-inner">
    <div class="about-grid">
      <div class="about-images reveal-left">
        <div class="about-img-main">
          @php $imgMain = sett_raw('about_image_main'); @endphp
          <img src="{{ $imgMain ? asset('assets/uploads/site/'.$imgMain) : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&q=80&auto=format' }}" alt="{{ __('front.about_tag') }}">
        </div>
        <div class="about-img-secondary">
          @php $imgSec = sett_raw('about_image_secondary'); @endphp
          <img src="{{ $imgSec ? asset('assets/uploads/site/'.$imgSec) : 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&q=80&auto=format' }}" alt="Classroom">
        </div>
        <div class="about-badge">
          <div class="about-badge-num">{{ sett_raw('about_years') ?: '25' }}+</div>
          <div class="about-badge-text">{{ __('front.about_badge_text') }}</div>
        </div>
      </div>

      <div class="reveal-right">
        <div class="about-section-eyebrow">{{ __('front.about_tag') }}</div>
        <h2 class="about-title">{{ sett('about_title') ?: __('front.about_title') }}</h2>
        <p class="about-desc">{{ sett('about_description') ?: __('front.about_text') }}</p>
        <div class="about-values">
          @php
            $valueIcons = ['🎯','🔬','🤝','🌟'];
            $valueColors = ['vi-blue','vi-red','vi-blue','vi-red'];
          @endphp
          @foreach([1,2,3,4] as $i)
          <div class="value-item">
            <div class="value-icon {{ $valueColors[$i-1] }}">{{ $valueIcons[$i-1] }}</div>
            <div class="value-item-text">
              <h5>{{ sett('about_value'.$i.'_title') ?: __('front.about_value'.$i.'_t') }}</h5>
              <p>{{ sett('about_value'.$i.'_desc') ?: __('front.about_value'.$i.'_d') }}</p>
            </div>
          </div>
          @endforeach
        </div>
        <a href="#contact" class="btn-primary">
          <span>{{ __('front.about_cta') }}</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ======= CONTACT ======= -->
<section id="contact">
  <div class="contact-inner">
    <div class="section-header reveal" style="text-align:center; max-width:600px; margin: 0 auto 64px;">
      <div class="section-eyebrow" style="justify-content:center">{{ __('front.contact_tag') }}</div>
      <h2 class="section-title">{{ __('front.contact_service_t') }}<br><span class="text-gradient-blue">{{ __('front.contact_service_span') }}</span></h2>
      <p class="section-sub" style="margin: 0 auto;">{{ __('front.contact_service_desc') }}</p>
    </div>
    <div class="contact-grid">
      <div>
        <div class="section-eyebrow">{{ __('front.contact_info_tag') }}</div>
        <h3 style="font-size:26px; font-weight:800; color:var(--navy); margin-bottom:8px; line-height:1.2;">{{ __('front.contact_easy_t') }}<br>{{ __('front.contact_easy_sub') }}</h3>
        <p style="font-size:14px; color:var(--text-muted); margin-bottom:0; line-height:1.8; font-weight:400;">{{ __('front.contact_easy_desc') }}</p>
        <div class="contact-info-card">
          <div class="contact-info-item">
            <div class="ci-icon">📍</div>
            <div class="ci-text">
              <h5>{{ __('front.contact_loc_label') }}</h5>
              <p>{{ sett('contact_address') ?: __('front.contact_loc_val') }}</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon">📞</div>
            <div class="ci-text">
              <h5>{{ __('front.contact_phone_label') }}</h5>
              <p>{{ sett_raw('contact_phone') ?: __('front.contact_ph_val') }}</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon">📧</div>
            <div class="ci-text">
              <h5>{{ __('front.contact_email_label') }}</h5>
              <p>{{ sett_raw('contact_email') ?: 'info@albahithacademy.edu.jo' }}</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="ci-icon">🕐</div>
            <div class="ci-text">
              <h5>{{ __('front.contact_hours_label') }}</h5>
              <p>{{ sett('contact_hours') ?: __('front.contact_hours_val') }}</p>
            </div>
          </div>
          @if(sett_raw('contact_whatsapp'))
          <div class="contact-info-item">
            <div class="ci-icon">💬</div>
            <div class="ci-text">
              <h5>WhatsApp</h5>
              <p><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', sett_raw('contact_whatsapp')) }}" target="_blank">{{ sett_raw('contact_whatsapp') }}</a></p>
            </div>
          </div>
          @endif
        </div>

        {{-- Social Media Icons --}}
        @php
          $socials = [
            'social_facebook'  => ['bi-facebook',  'Facebook'],
            'social_instagram' => ['bi-instagram', 'Instagram'],
            'social_youtube'   => ['bi-youtube',   'YouTube'],
            'social_twitter'   => ['bi-twitter-x', 'Twitter'],
            'social_tiktok'    => ['bi-tiktok',    'TikTok'],
            'social_snapchat'  => ['bi-snapchat',  'Snapchat'],
            'social_whatsapp'  => ['bi-whatsapp',  'WhatsApp'],
          ];
          $hasSocial = collect($socials)->keys()->filter(fn($k) => sett_raw($k))->isNotEmpty();
        @endphp
        @if($hasSocial)
        <div class="d-flex gap-2 flex-wrap mt-4">
          @foreach($socials as $key => [$icon, $label])
            @if(sett_raw($key))
              <a href="{{ sett_raw($key) }}" target="_blank" rel="noopener"
                 class="social-icon-btn" title="{{ $label }}"
                 style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:var(--primary,#1e40af);color:#fff;font-size:18px;text-decoration:none">
                <i class="bi {{ $icon }}"></i>
              </a>
            @endif
          @endforeach
        </div>
        @endif

        {{-- App Store Buttons --}}
        @if(sett_raw('app_google_play') || sett_raw('app_store'))
        <div class="d-flex gap-2 flex-wrap mt-3">
          @if(sett_raw('app_google_play'))
          <a href="{{ sett_raw('app_google_play') }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;">
            <i class="bi bi-google-play" style="font-size:20px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:10px;display:block">GET IT ON</small>Google Play</div>
          </a>
          @endif
          @if(sett_raw('app_store'))
          <a href="{{ sett_raw('app_store') }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;">
            <i class="bi bi-apple" style="font-size:20px"></i>
            <div style="line-height:1.1"><small style="opacity:.7;font-size:10px;display:block">DOWNLOAD ON THE</small>App Store</div>
          </a>
          @endif
        </div>
        @endif
      </div>

      <div class="contact-form reveal-right">
        <h4 style="font-size:22px; font-weight:800; color:var(--navy); margin-bottom:28px;">{{ __('front.contact_form_title') }}</h4>

        @if(session('contact_success'))
          <div class="form-success-msg" style="background:#d1fae5;color:#065f46;padding:14px 18px;border-radius:8px;margin-bottom:20px;font-weight:600;">
            {{ __('front.contact_success') }}
          </div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label>{{ __('front.contact_name') }}</label>
              <input type="text" name="name" value="{{ old('name') }}"
                     placeholder="{{ __('front.contact_name_ph') }}" required>
              @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>{{ __('front.contact_phone') }}</label>
              <input type="tel" name="phone" value="{{ old('phone') }}"
                     placeholder="{{ __('front.contact_phone_ph') }}">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:18px;">
            <label>{{ __('front.contact_email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="email@example.com" required>
            @error('email')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group" style="margin-bottom:18px;">
            <label>{{ __('front.contact_subject') }}</label>
            <select name="subject">
              <option value="">{{ __('front.contact_subject_ph') }}</option>
              <option value="{{ __('front.contact_subject_courses') }}">{{ __('front.contact_subject_courses') }}</option>
              <option value="{{ __('front.contact_subject_exams') }}">{{ __('front.contact_subject_exams') }}</option>
              <option value="{{ __('front.contact_subject_support') }}">{{ __('front.contact_subject_support') }}</option>
              <option value="{{ __('front.contact_subject_partnership') }}">{{ __('front.contact_subject_partnership') }}</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:24px;">
            <label>{{ __('front.contact_message') }}</label>
            <textarea name="message" placeholder="{{ __('front.contact_message_ph') }}" required>{{ old('message') }}</textarea>
            @error('message')<span class="form-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-submit">
            <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">
              <span>{{ __('front.contact_send') }}</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
          </div>
          <p class="form-note" style="margin-top:14px;">{{ __('front.contact_note') }}</p>
        </form>
      </div>
    </div>
  </div>
</section>


<!-- ── OVERLAY HTML ── -->
<div id="app-overlay">
  <!-- NAV -->
  <div class="ov-nav">
    <div class="ov-logo" onclick="APP.closeOverlay()">
      <div class="ov-logo-icon">ب</div>
      <div class="ov-logo-text">
        <strong>{{ __('front.overlay_logo_name') }}</strong>
        <span>{{ __('front.overlay_logo_sub') }}</span>
      </div>
    </div>
    <div class="ov-breadcrumb" id="ov-bc"></div>
    <button class="ov-back" id="ov-back" onclick="APP.back()" hidden>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      {{ __('front.overlay_back') }}
    </button>
    <button class="ov-close" onclick="APP.closeOverlay()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      {{ __('front.overlay_close') }}
    </button>
  </div>

  <!-- PAGE: GRADE LIST -->
  <div class="ov-page" id="ovp-grades">
    <div class="ov-hero">
      <div class="ov-hero-inner">
        <div class="ov-eyebrow">{{ __('front.overlay_grades_eye') }}</div>
        <h1 class="ov-hero-title">{{ __('front.overlay_grades_t') }}</h1>
        <p class="ov-hero-sub">{{ __('front.overlay_grades_sub') }}</p>
      </div>
    </div>
    <div class="ov-body">
      <div class="ov-grade-grid" id="ov-grade-list"></div>
    </div>
  </div>

  <!-- PAGE: SUBJECTS (grade) — with Semester Filter Tabs -->
  <div class="ov-page" id="ovp-subjects">
    <div class="ov-hero">
      <div class="ov-hero-inner">
        <div class="ov-eyebrow" id="subj-ey">{{ __('front.overlay_grades_eye') }}</div>
        <h1 class="ov-hero-title" id="subj-ttl">{{ __('front.overlay_subj_t') }}</h1>
        <p class="ov-hero-sub">{{ __('front.overlay_subj_sub') }}</p>
      </div>
    </div>
    <div class="ov-body">
      <div class="ov-sem-tabs">
        <button class="ov-sem-tab" id="ov-tab-1" onclick="APP.setSemester(1)">
          <span class="ov-sem-tab-roman">I</span>
          <span class="ov-sem-tab-label">{{ __('front.overlay_sem1') }}</span>
        </button>
        <button class="ov-sem-tab" id="ov-tab-2" onclick="APP.setSemester(2)">
          <span class="ov-sem-tab-roman">II</span>
          <span class="ov-sem-tab-label">{{ __('front.overlay_sem2') }}</span>
        </button>
      </div>
      <div class="ov-subj-grid" id="ov-subj-list"></div>
    </div>
  </div>

  <!-- PAGE: COURSES -->
  <div class="ov-page" id="ovp-courses">
    <div class="ov-hero">
      <div class="ov-hero-inner">
        <div class="ov-eyebrow" id="crs-ey">{{ __('front.overlay_crs_eye') }}</div>
        <h1 class="ov-hero-title" id="crs-ttl">{{ __('front.overlay_crs_t') }}</h1>
        <p class="ov-hero-sub" id="crs-sb">{{ __('front.overlay_crs_sub') }}</p>
      </div>
    </div>
    <div class="ov-body">
      <div class="ov-course-grid" id="ov-course-list"></div>
    </div>
  </div>

  <!-- PAGE: TAWJIHI — GENERATIONS -->
  <div class="ov-page" id="ovp-tawjihi">
    <div class="ov-hero">
      <div class="ov-hero-inner">
        <div class="ov-eyebrow">{{ __('front.overlay_tawjihi_eye') }}</div>
        <h1 class="ov-hero-title">{{ __('front.overlay_tawjihi_t') }}</h1>
        <p class="ov-hero-sub">{{ __('front.overlay_tawjihi_sub') }}</p>
      </div>
    </div>
    <div class="ov-body">
      <div class="ov-section-hd">
        <div class="ov-section-eye">{{ __('front.overlay_gen_eye') }}</div>
        <h2 class="ov-section-title">{{ __('front.overlay_gen_t') }}</h2>
        <p class="ov-section-sub">{{ __('front.overlay_gen_sub') }}</p>
      </div>
      <div class="ov-gen-grid" id="ov-gen-list"></div>
    </div>
  </div>

  <!-- PAGE: FIELDS -->
  <div class="ov-page" id="ovp-fields">
    <div class="ov-hero">
      <div class="ov-hero-inner">
        <div class="ov-eyebrow" id="fld-ey">{{ __('front.overlay_fields_eye') }}</div>
        <h1 class="ov-hero-title">{{ __('front.overlay_fields_t') }}</h1>
        <p class="ov-hero-sub">{{ __('front.overlay_fields_sub') }}</p>
      </div>
    </div>
    <div class="ov-body">
      <div class="ov-field-grid" id="ov-field-list"></div>
    </div>
  </div>

  <!-- PAGE: FIELD SUBJECTS -->
  <div class="ov-page" id="ovp-fsubjects">
    <div class="ov-hero">
      <div class="ov-hero-inner">
        <div class="ov-eyebrow" id="fs-ey">{{ __('front.overlay_fsubj_eye') }}</div>
        <h1 class="ov-hero-title" id="fs-ttl">{{ __('front.overlay_fsubj_t') }}</h1>
        <p class="ov-hero-sub" id="fs-sb">{{ __('front.overlay_fsubj_sub') }}</p>
      </div>
    </div>
    <div class="ov-body">
      <div class="ov-subj-section" id="ov-comp-sec">
        <div class="ov-subj-sec-hd">
          <span class="ov-subj-badge sb-comp">{{ __('front.overlay_comp_badge') }}</span>
          <div>
            <div class="ov-subj-sec-label">{{ __('front.overlay_comp_label') }}</div>
            <div class="ov-subj-sec-count" id="ov-comp-count"></div>
          </div>
        </div>
        <div class="ov-subj-grid" id="ov-comp-list"></div>
      </div>
      <div class="ov-subj-section">
        <div class="ov-subj-sec-hd">
          <span class="ov-subj-badge sb-elec">{{ __('front.overlay_elec_badge') }}</span>
          <div>
            <div class="ov-subj-sec-label">{{ __('front.overlay_elec_label') }}</div>
            <div class="ov-subj-sec-count" id="ov-elec-count"></div>
          </div>
        </div>
        <div class="ov-subj-grid" id="ov-elec-list"></div>
      </div>
    </div>
  </div>

</div><!-- end #app-overlay -->

@endsection

@push('data')
<script>
window.APP_DATA = @json($overlayData);
</script>
@endpush
