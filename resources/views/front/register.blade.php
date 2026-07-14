@extends('layouts.front')
@section('title', __('front.auth_register_title') . ' — ' . __('front.site_name'))

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
        <h1 class="auth-hero-title">{{ __('front.auth_register_hero_title') }}</h1>
        <p class="auth-hero-sub">{{ __('front.auth_register_hero_sub') }}</p>
        <div class="auth-hero-stats">
          <div class="auth-stat">
            <div class="auth-stat-num">+2,400</div>
            <div class="auth-stat-label">{{ __('front.stat_students') }}</div>
          </div>
          <div class="auth-stat">
            <div class="auth-stat-num">25+</div>
            <div class="auth-stat-label">{{ app()->getLocale() === 'ar' ? 'سنة خبرة' : 'Years' }}</div>
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
          <h2 class="auth-card-title">{{ __('front.auth_register_title') }}</h2>
          <p class="auth-card-sub">{{ __('front.auth_register_sub') }}</p>
        </div>

        @if ($errors->any())
          <div class="auth-alert auth-alert-error">
            <ul style="margin:0;padding-inline-start:16px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('student.register.post') }}" method="POST" class="auth-form">
          @csrf

          <div class="auth-field">
            <label for="name">{{ __('front.auth_full_name') }}</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}"
                   placeholder="{{ __('front.auth_full_name_ph') }}"
                   autocomplete="name" required>
          </div>

          <div class="auth-field">
            <label for="national_id">{{ app()->getLocale() === 'ar' ? 'الرقم الوطني *' : 'National ID *' }}</label>
            <input type="text" id="national_id" name="national_id"
                   value="{{ old('national_id') }}"
                   placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل رقمك الوطني' : 'Enter your national ID' }}"
                   required>
          </div>

          <div class="auth-field-row">
            <div class="auth-field">
              <label for="email">{{ __('front.auth_email_label') }} <small style="opacity:.6">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'optional' }})</small></label>
              <input type="email" id="email" name="email"
                     value="{{ old('email') }}"
                     placeholder="email@example.com"
                     autocomplete="email">
            </div>
            <div class="auth-field">
              <label for="phone">{{ __('front.auth_phone_label') }}</label>
              <input type="tel" id="phone" name="phone"
                     value="{{ old('phone') }}"
                     placeholder="+962 7XX XXXXX">
            </div>
          </div>


          <div class="auth-field">
            <label for="password">{{ __('front.auth_password_label') }}</label>
            <div class="auth-input-wrap">
              <input type="password" id="password" name="password"
                     placeholder="{{ __('front.auth_password_ph') }}"
                     autocomplete="new-password" required>
              <button type="button" class="auth-eye-btn" onclick="togglePass('password')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <div class="auth-field">
            <label for="password_confirmation">{{ __('front.auth_confirm_password') }}</label>
            <div class="auth-input-wrap">
              <input type="password" id="password_confirmation" name="password_confirmation"
                     placeholder="{{ __('front.auth_confirm_ph') }}"
                     autocomplete="new-password" required>
              <button type="button" class="auth-eye-btn" onclick="togglePass('password_confirmation')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <div class="auth-field" style="margin-bottom:22px;">
            <label class="auth-checkbox">
              <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
              <span>
                {{ __('front.auth_agree') }}
                <a href="#" style="color:var(--blue);font-weight:600;">{{ __('front.auth_terms_link') }}</a>
              </span>
            </label>
          </div>

          <button type="submit" class="auth-submit-btn">
            {{ __('front.auth_register_btn') }}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="{{ app()->getLocale() === 'ar' ? 'M19 12H5M12 19l-7-7 7-7' : 'M5 12h14M12 5l7 7-7 7' }}"/>
            </svg>
          </button>
        </form>

        <p class="auth-switch">
          {{ __('front.auth_have_account') }}
          <a href="{{ route('student.login') }}">{{ __('front.auth_sign_in_link') }}</a>
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

.auth-form-side {
  width: 520px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 48px;
  background: var(--gray-light, #f7f9fc);
  overflow-y: auto;
}
.auth-card { width: 100%; max-width: 420px; }
.auth-card-header { margin-bottom: 24px; }
.auth-card-title  { font-size: 24px; font-weight: 800; color: var(--navy); margin-bottom: 6px; }
.auth-card-sub    { color: var(--text-muted, #64748b); font-size: 14px; }

.auth-alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; font-weight: 500; }
.auth-alert-error { background: #fee2e2; color: #991b1b; }

.auth-field { margin-bottom: 16px; }
.auth-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.auth-field label { display: block; font-size: 13px; font-weight: 600; color: var(--navy); margin-bottom: 5px; }
.auth-field input, .auth-field select {
  width: 100%;
  padding: 10px 14px;
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
  top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #94a3b8; padding: 0; display: flex;
}

.auth-checkbox { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #64748b; cursor: pointer; line-height: 1.5; }
.auth-checkbox input { width: auto; margin: 0; margin-top: 2px; flex-shrink: 0; }

.auth-submit-btn {
  width: 100%; padding: 13px;
  background: var(--blue); color: #fff;
  border: none; border-radius: 10px;
  font-size: 15px; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  font-family: inherit; transition: opacity .2s;
}
.auth-submit-btn:hover { opacity: .9; }

.auth-switch { margin-top: 18px; text-align: center; font-size: 13px; color: #64748b; }
.auth-switch a { color: var(--blue); font-weight: 600; text-decoration: none; }
.auth-switch a:hover { text-decoration: underline; }

@media (max-width: 768px) {
  .auth-inner   { flex-direction: column; }
  .auth-hero    { padding: 40px 24px; }
  .auth-form-side { width: 100%; padding: 32px 20px; }
  .auth-hero-title { font-size: 26px; }
  .auth-field-row { grid-template-columns: 1fr; }
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
