@extends('layouts.front')
@section('title', __('front.auth_login_title') . ' — ' . __('front.site_name'))

@section('content')
<section class="auth-section">
  <div class="auth-inner">

    {{-- Hero side --}}
    <div class="auth-hero">
      <div class="auth-hero-content">
        <div class="auth-logo">
          <div class="auth-logo-icon">ب</div>
          <div class="auth-logo-text">
            <strong>{{ __('front.site_name') }}</strong>
            <span>{{ __('front.site_tagline') }}</span>
          </div>
        </div>
        <h1 class="auth-hero-title">{{ __('front.auth_login_hero_title') }}</h1>
        <p class="auth-hero-sub">{{ __('front.auth_login_hero_sub') }}</p>
        <div class="auth-hero-stats">
          <div class="auth-stat">
            <div class="auth-stat-num">+2,400</div>
            <div class="auth-stat-label">{{ __('front.stat_students') }}</div>
          </div>
          <div class="auth-stat">
            <div class="auth-stat-num">120</div>
            <div class="auth-stat-label">{{ __('front.stat_teachers') }}</div>
          </div>
          <div class="auth-stat">
            <div class="auth-stat-num">98%</div>
            <div class="auth-stat-label">{{ __('front.stat_satisfaction') }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Form side --}}
    <div class="auth-form-side">
      <div class="auth-card">
        <div class="auth-card-header">
          <h2 class="auth-card-title">{{ __('front.auth_login_title') }}</h2>
          <p class="auth-card-sub">{{ __('front.auth_login_sub') }}</p>
        </div>

        @if ($errors->any())
          <div class="auth-alert auth-alert-error">
            {{ $errors->first() }}
          </div>
        @endif

        <form action="{{ route('student.login.post') }}" method="POST" class="auth-form">
          @csrf

          <div class="auth-field">
            <label for="login">{{ app()->getLocale() === 'ar' ? 'الرقم الوطني أو البريد الإلكتروني' : 'National ID or Email' }}</label>
            <input type="text" id="login" name="login"
                   value="{{ old('login') }}"
                   placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل رقمك الوطني' : 'Enter your national ID' }}"
                   autocomplete="username" required>
          </div>

          <div class="auth-field">
            <label for="password">{{ __('front.auth_password_label') }}</label>
            <div class="auth-input-wrap">
              <input type="password" id="password" name="password"
                     placeholder="{{ __('front.auth_password_ph') }}"
                     autocomplete="current-password" required>
              <button type="button" class="auth-eye-btn" onclick="togglePass('password')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <div class="auth-row">
            <label class="auth-checkbox">
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              <span>{{ __('front.auth_remember') }}</span>
            </label>
          </div>

          <button type="submit" class="auth-submit-btn">
            {{ __('front.auth_login_btn') }}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="{{ app()->getLocale() === 'ar' ? 'M19 12H5M12 19l-7-7 7-7' : 'M5 12h14M12 5l7 7-7 7' }}"/>
            </svg>
          </button>
        </form>

        <p class="auth-switch">
          {{ __('front.auth_no_account') }}
          <a href="{{ route('student.register') }}">{{ __('front.auth_create_account') }}</a>
        </p>
      </div>
    </div>

  </div>
</section>
@endsection

@push('styles')
<style>
.auth-section { min-height: 100vh; display: flex; align-items: stretch; }
.auth-inner   { display: flex; width: 100%; min-height: 100vh; }

/* Hero */
.auth-hero {
  flex: 1;
  background: var(--navy);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 48px;
  position: relative;
  overflow: hidden;
}
.auth-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(27,79,216,.3) 0%, transparent 60%);
  pointer-events: none;
}
.auth-hero-content { position: relative; z-index: 1; max-width: 400px; }
.auth-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
.auth-logo-icon {
  width: 48px; height: 48px;
  background: var(--blue);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; font-weight: 800; color: #fff;
}
.auth-logo-text strong { display: block; color: #fff; font-size: 15px; font-weight: 700; }
.auth-logo-text span   { color: rgba(255,255,255,.55); font-size: 12px; }
.auth-hero-title { font-size: 38px; font-weight: 800; color: #fff; line-height: 1.2; margin-bottom: 16px; }
.auth-hero-sub   { color: rgba(255,255,255,.65); font-size: 15px; line-height: 1.7; margin-bottom: 40px; }
.auth-hero-stats { display: flex; gap: 32px; }
.auth-stat-num   { font-size: 26px; font-weight: 800; color: #fff; }
.auth-stat-label { font-size: 12px; color: rgba(255,255,255,.5); margin-top: 2px; }

/* Form side */
.auth-form-side {
  width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 48px;
  background: var(--gray-light, #f7f9fc);
}
.auth-card { width: 100%; max-width: 380px; }
.auth-card-header { margin-bottom: 28px; }
.auth-card-title  { font-size: 26px; font-weight: 800; color: var(--navy); margin-bottom: 6px; }
.auth-card-sub    { color: var(--text-muted, #64748b); font-size: 14px; }

/* Alert */
.auth-alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
.auth-alert-error { background: #fee2e2; color: #991b1b; }
.auth-alert-success { background: #d1fae5; color: #065f46; }

/* Fields */
.auth-field { margin-bottom: 18px; }
.auth-field label { display: block; font-size: 13px; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
.auth-field input, .auth-field select {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  background: #fff;
  transition: border-color .2s;
  font-family: inherit;
}
.auth-field input:focus, .auth-field select:focus {
  outline: none;
  border-color: var(--blue);
  box-shadow: 0 0 0 3px rgba(27,79,216,.1);
}
.auth-input-wrap { position: relative; }
.auth-input-wrap input { padding-inline-end: 44px; }
.auth-eye-btn {
  position: absolute;
  inset-inline-end: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  padding: 0;
  display: flex;
}

/* Row */
.auth-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
.auth-checkbox { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; cursor: pointer; }
.auth-checkbox input { width: auto; margin: 0; }

/* Submit */
.auth-submit-btn {
  width: 100%;
  padding: 13px;
  background: var(--blue);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: inherit;
  transition: opacity .2s;
}
.auth-submit-btn:hover { opacity: .9; }

/* Switch */
.auth-switch { margin-top: 20px; text-align: center; font-size: 13px; color: #64748b; }
.auth-switch a { color: var(--blue); font-weight: 600; text-decoration: none; }
.auth-switch a:hover { text-decoration: underline; }

/* Responsive */
@media (max-width: 768px) {
  .auth-inner   { flex-direction: column; }
  .auth-hero    { padding: 40px 24px; }
  .auth-form-side { width: 100%; padding: 40px 24px; }
  .auth-hero-title { font-size: 26px; }
  .auth-hero-stats { gap: 20px; }
}
</style>
@endpush

@push('scripts')
<script>
function togglePass(id) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
