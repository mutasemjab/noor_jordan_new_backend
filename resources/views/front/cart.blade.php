@extends('layouts.front')
@section('title', __('front.shopping_cart') . ' — ' . __('front.site_name'))

@section('content')

<section style="background:linear-gradient(135deg,var(--navy),#1a2a4a);padding:80px 5% 40px;">
  <div style="max-width:1200px;margin:0 auto;">
    <h1 style="color:white;font-size:32px;font-weight:900;margin:0 0 8px;">🛒 {{ __('front.shopping_cart') }}</h1>
    <p style="color:rgba(255,255,255,0.65);font-size:14px;">{{ __('front.review_courses') }}</p>
  </div>
</section>

<section style="padding:48px 5%;background:var(--bg-soft);min-height:60vh;">
  <div style="max-width:1200px;margin:0 auto;">

    @if(session('cart_removed'))
    <div style="background:#fef2f2;color:#991b1b;padding:12px 20px;border-radius:10px;margin-bottom:24px;font-weight:600;">✓ {{ app()->getLocale()==='ar'?'تم حذف الدورة من السلة.':'Course removed from cart.' }}</div>
    @endif

    @if($courses->isEmpty())
    <div style="text-align:center;padding:100px 0;">
      <div style="font-size:64px;margin-bottom:20px;">🛒</div>
      <h2 style="font-size:24px;font-weight:800;color:var(--navy);margin-bottom:10px;">{{ __('front.cart_empty') }}</h2>
      <p style="color:var(--text-muted);margin-bottom:28px;">{{ __('front.no_courses_added') }}</p>
      <a href="{{ route('courses.index') }}" class="btn-primary" style="text-decoration:none;padding:14px 32px;">{{ __('front.browse_courses') }}</a>
    </div>

    @else
    <div style="display:grid;grid-template-columns:1fr 320px;gap:32px;align-items:start;" class="cart-grid">

      {{-- Items --}}
      <div>
        @foreach($courses as $course)
        <div style="background:white;border-radius:16px;padding:20px;margin-bottom:16px;display:flex;gap:16px;align-items:center;box-shadow:0 2px 14px rgba(0,0,0,0.06);" class="cart-item">
          <div style="width:90px;height:70px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#e2e8f0;">
            @if($course->thumbnail)
              <img src="{{ asset('assets/uploads/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover;">
            @else
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:28px;">📚</div>
            @endif
          </div>
          <div style="flex:1;min-width:0;">
            <a href="{{ route('courses.show',$course->id) }}" style="font-size:15px;font-weight:800;color:var(--navy);text-decoration:none;display:block;margin-bottom:4px;">{{ $course->title }}</a>
            @if($course->teacher)<div style="font-size:12px;color:var(--text-muted);margin-bottom:6px;">{{ $course->teacher->name }}</div>@endif
            <div style="display:flex;gap:10px;font-size:12px;color:var(--text-muted);">
              <span>⭐ {{ number_format($course->average_rating,1) }}</span>
              <span>👥 {{ $course->total_students }}</span>
              @if($course->duration_hours)<span>⏱ {{ $course->duration_hours }}h</span>@endif
            </div>
          </div>
          <div style="text-align:end;flex-shrink:0;">
            <div style="font-size:20px;font-weight:900;color:var(--navy);margin-bottom:8px;">
              {{ $course->is_free || $course->price==0 ? __('front.courses_free') : $course->price.' '.__('front.courses_jod') }}
            </div>
            <form method="POST" action="{{ route('cart.remove',$course->id) }}">
              @csrf
              <button type="submit" style="background:none;border:none;color:#ef4444;font-size:12px;font-weight:600;cursor:pointer;padding:4px 8px;border-radius:6px;background:#fef2f2;">🗑 {{ app()->getLocale()==='ar'?'حذف':'Remove' }}</button>
            </form>
          </div>
        </div>
        @endforeach

        <a href="{{ route('courses.index') }}" style="color:var(--blue);font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-top:8px;">← {{ __('front.continue_shopping') }}</a>
      </div>

      {{-- Summary --}}
      <div style="background:white;border-radius:18px;padding:28px;box-shadow:0 2px 20px rgba(0,0,0,0.08);position:sticky;top:100px;">
        <h3 style="font-size:18px;font-weight:800;color:var(--navy);margin-bottom:24px;">{{ __('front.order_summary') }}</h3>
        <div style="display:flex;justify-content:space-between;margin-bottom:12px;font-size:14px;">
          <span style="color:var(--text-muted);">{{ __('front.subtotal') }}</span>
          <span style="font-weight:700;color:var(--navy);">{{ number_format($subtotal,2) }} {{ __('front.courses_jod') }}</span>
        </div>
        @if($discount > 0)
        <div style="display:flex;justify-content:space-between;margin-bottom:12px;font-size:14px;">
          <span style="color:var(--text-muted);">{{ __('front.discount') }}</span>
          <span style="font-weight:700;color:#10b981;">- {{ number_format($discount,2) }} {{ __('front.courses_jod') }}</span>
        </div>
        @endif
        <div style="border-top:2px solid #f1f5f9;margin:16px 0;"></div>
        <div style="display:flex;justify-content:space-between;margin-bottom:24px;font-size:18px;">
          <span style="font-weight:800;color:var(--navy);">{{ __('front.total') }}</span>
          <span style="font-weight:900;color:var(--navy);">{{ number_format($total,2) }} {{ __('front.courses_jod') }}</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="btn-primary" style="display:flex;justify-content:center;padding:15px;text-decoration:none;font-size:15px;">{{ __('front.checkout') }} →</a>
        <p style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:12px;">{{ __('front.course_activation_note') }}</p>
      </div>

    </div>
    @endif

  </div>
</section>

@endsection

@push('styles')
<style>
@media(max-width:800px){
  .cart-grid{ grid-template-columns:1fr !important; }
  .cart-item{ flex-wrap:wrap; }
}
</style>
@endpush
