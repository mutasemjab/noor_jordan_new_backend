@extends('layouts.front')
@section('title', __('front.activate_card_title') . ' — ' . __('front.site_name'))

@section('content')

<section style="background:linear-gradient(135deg,var(--navy),#1a2a4a);padding:80px 5% 40px;">
  <div style="max-width:700px;margin:0 auto;">
    <h1 style="color:white;font-size:30px;font-weight:900;margin:0 0 8px;">🎴 {{ __('front.activate_card_title') }}</h1>
    <p style="color:rgba(255,255,255,0.65);font-size:14px;">{{ __('front.activate_card_desc') }}</p>
  </div>
</section>

<section style="padding:48px 5%;background:var(--bg-soft);min-height:60vh;">
  <div style="max-width:700px;margin:0 auto;display:grid;grid-template-columns:1fr 260px;gap:28px;align-items:start;" class="checkout-grid">

    {{-- Card input panel --}}
    <div style="background:white;border-radius:20px;padding:36px;box-shadow:0 4px 28px rgba(0,0,0,0.09);">

      @if($errors->has('card_number'))
      <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:14px 18px;border-radius:12px;margin-bottom:24px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
        <span>⚠️</span> {{ $errors->first('card_number') }}
      </div>
      @endif

      @if(session('activation_error'))
      <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:14px 18px;border-radius:12px;margin-bottom:24px;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
        <span>⚠️</span> {{ session('activation_error') }}
      </div>
      @endif

      <div style="text-align:center;margin-bottom:32px;">
        <div style="width:72px;height:72px;background:linear-gradient(135deg,var(--blue),#1a6bff);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 16px;">🎴</div>
        <h2 style="font-size:20px;font-weight:800;color:var(--navy);margin:0 0 6px;">{{ __('front.enter_card_number') }}</h2>
        <p style="font-size:13px;color:var(--text-muted);">{{ __('front.card_number_hint') }}</p>
      </div>

      @auth('student')
      <form method="POST" action="{{ route('checkout.activate') }}">
        @csrf
        <div style="margin-bottom:24px;">
          <label style="font-size:13px;font-weight:700;color:var(--navy);display:block;margin-bottom:10px;">{{ __('front.card_number_label') }}</label>
          <input
            type="text"
            name="card_number"
            value="{{ old('card_number') }}"
            placeholder="{{ __('front.card_number_ph') }}"
            autocomplete="off"
            autofocus
            style="width:100%;padding:16px 20px;border-radius:12px;border:2px solid {{ $errors->has('card_number') ? '#ef4444' : '#e2e8f0' }};font-size:18px;letter-spacing:3px;outline:none;box-sizing:border-box;font-family:monospace;text-align:center;transition:border .2s;"
            onfocus="this.style.borderColor='var(--blue)'"
            onblur="this.style.borderColor='{{ $errors->has('card_number') ? '#ef4444' : '#e2e8f0' }}'"
          >
        </div>
        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:16px;font-size:16px;font-weight:800;">
          ✅ {{ __('front.activate_now') }}
        </button>
      </form>
      @else
      <div style="text-align:center;padding:16px 0;">
        <p style="color:var(--text-muted);font-size:14px;margin-bottom:20px;">{{ app()->getLocale()==='ar'?'يجب تسجيل الدخول لتفعيل الكارت.':'You must be logged in to activate a card.' }}</p>
        <a href="{{ route('student.login') }}" class="btn-primary" style="text-decoration:none;padding:14px 32px;">{{ __('front.nav_login') }}</a>
      </div>
      @endauth

      <p style="font-size:12px;color:var(--text-muted);text-align:center;margin-top:20px;line-height:1.6;">
        {{ __('front.card_instant_note') }}
      </p>
    </div>

    {{-- Order summary --}}
    <div>
      <div style="background:white;border-radius:18px;padding:24px;box-shadow:0 2px 18px rgba(0,0,0,0.08);position:sticky;top:100px;">
        <h3 style="font-size:15px;font-weight:800;color:var(--navy);margin-bottom:16px;">{{ __('front.your_order') }}</h3>
        @foreach($courses as $course)
        <div style="display:flex;gap:10px;margin-bottom:12px;align-items:center;">
          <div style="width:44px;height:34px;border-radius:7px;overflow:hidden;flex-shrink:0;background:#e2e8f0;">
            @if($course->thumbnail)
              <img src="{{ asset('assets/uploads/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover;">
            @else
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:14px;">📚</div>
            @endif
          </div>
          <div style="font-size:12px;font-weight:600;color:var(--navy);line-height:1.3;flex:1;">{{ $course->title }}</div>
        </div>
        @endforeach

        <div style="border-top:1px solid #f1f5f9;margin-top:14px;padding-top:14px;">
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--text-muted);">{{ app()->getLocale()==='ar'?'عدد الدورات':'Courses' }}</span>
            <span style="font-weight:700;color:var(--navy);">{{ $courses->count() }}</span>
          </div>
        </div>

        <div style="margin-top:16px;display:flex;flex-direction:column;gap:6px;font-size:11px;color:var(--text-muted);">
          <div style="display:flex;gap:6px;align-items:center;">⚡ {{ app()->getLocale()==='ar'?'تفعيل فوري':'Instant activation' }}</div>
          <div style="display:flex;gap:6px;align-items:center;">♾️ {{ __('front.course_sidebar_access') }}</div>
        </div>
      </div>
      <div style="margin-top:12px;text-align:center;">
        <a href="{{ route('cart.index') }}" style="color:var(--text-muted);font-size:12px;text-decoration:none;">← {{ __('front.cart') }}</a>
      </div>
    </div>

  </div>
</section>

@endsection

@push('styles')
<style>
@media(max-width:700px){ .checkout-grid{ grid-template-columns:1fr !important; } }
</style>
@endpush
