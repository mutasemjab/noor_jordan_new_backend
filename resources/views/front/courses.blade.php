@extends('layouts.front')
@section('title', __('front.courses_page_title') . ' — ' . __('front.site_name'))

@section('content')

{{-- ── PAGE HERO ── --}}
<section style="background:linear-gradient(135deg,var(--navy) 0%,#1a2a4a 100%);padding:100px 5% 60px;">
  <div style="max-width:1200px;margin:0 auto;">
    <div style="color:rgba(255,255,255,0.6);font-size:13px;margin-bottom:8px;">{{ __('front.nav_courses') }}</div>
    <h1 style="color:white;font-size:clamp(28px,4vw,46px);font-weight:900;margin:0 0 12px;">{{ __('front.courses_page_title') }}</h1>
    <p style="color:rgba(255,255,255,0.7);font-size:16px;max-width:560px;">{{ __('front.courses_page_desc') }}</p>

    {{-- Search + Filters --}}
    <form method="GET" action="{{ route('courses.index') }}" style="margin-top:32px;display:flex;gap:12px;flex-wrap:wrap;">
      @if(request('locale')) <input type="hidden" name="locale" value="{{ request('locale') }}"> @endif
      <input type="text" name="q" value="{{ request('q') }}"
             placeholder="{{ __('front.courses_search_ph') }}"
             style="flex:1;min-width:200px;padding:12px 18px;border-radius:12px;border:none;font-size:14px;background:rgba(255,255,255,0.12);color:white;outline:none;"
             oninput="this.style.background='rgba(255,255,255,0.18)'">
      <select name="category" style="padding:12px 18px;border-radius:12px;border:none;font-size:14px;background:rgba(255,255,255,0.12);color:white;cursor:pointer;">
        <option value="">{{ __('front.courses_filter_all_cat') }}</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ request('category') === $cat->id ? 'selected' : '' }}
                  style="color:var(--navy);">
            {{ app()->getLocale() === 'ar' ? $cat->name_ar : ($cat->name_en ?? $cat->name_ar) }}
          </option>
        @endforeach
      </select>
      <select name="sort" style="padding:12px 18px;border-radius:12px;border:none;font-size:14px;background:rgba(255,255,255,0.12);color:white;cursor:pointer;">
        <option value="popular"   {{ request('sort','popular') === 'popular'   ? 'selected' : '' }} style="color:var(--navy);">{{ __('front.courses_sort_popular') }}</option>
        <option value="newest"    {{ request('sort') === 'newest'    ? 'selected' : '' }} style="color:var(--navy);">{{ __('front.courses_sort_newest') }}</option>
        <option value="top-rated" {{ request('sort') === 'top-rated' ? 'selected' : '' }} style="color:var(--navy);">{{ __('front.courses_sort_rated') }}</option>
        <option value="cheap"     {{ request('sort') === 'cheap'     ? 'selected' : '' }} style="color:var(--navy);">{{ __('front.courses_sort_cheap') }}</option>
      </select>
      <button type="submit" class="btn-primary" style="padding:12px 28px;">{{ __('front.search') }}</button>
    </form>
  </div>
</section>

{{-- ── RESULTS ── --}}
<section style="padding:48px 5%;background:var(--bg-soft);min-height:50vh;">
  <div style="max-width:1200px;margin:0 auto;">

    @if(session('cart_added'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:24px;font-weight:600;display:flex;justify-content:space-between;align-items:center;">
      <span>✓ {{ session('cart_added') }} — {{ __('front.courses_add_cart') }}</span>
      <a href="{{ route('cart.index') }}" style="color:#065f46;text-decoration:underline;">{{ __('front.cart') }} →</a>
    </div>
    @endif

    {{-- Count --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
      <p style="color:var(--text-muted);font-size:14px;">
        {{ __('front.courses_per_page', ['from'=>$courses->firstItem()??0,'to'=>$courses->lastItem()??0,'total'=>$courses->total()]) }}
      </p>
    </div>

    @if($courses->isEmpty())
      <div style="text-align:center;padding:80px 0;color:var(--text-muted);">
        <div style="font-size:48px;margin-bottom:16px;">📭</div>
        <p style="font-size:16px;">{{ __('front.courses_no_results') }}</p>
        <a href="{{ route('courses.index') }}" style="color:var(--blue);font-weight:600;">{{ __('front.courses_filter_all_tag') }} →</a>
      </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:28px;">
      @foreach($courses as $course)
      <div style="background:white;border-radius:18px;overflow:hidden;box-shadow:0 2px 20px rgba(0,0,0,0.07);display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 40px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow='0 2px 20px rgba(0,0,0,0.07)'">
        {{-- Thumb --}}
        <a href="{{ route('courses.show', $course->id) }}" style="display:block;position:relative;height:180px;overflow:hidden;background:#e2e8f0;">
          @if($course->thumbnail)
            <img src="{{ asset('assets/uploads/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;background:linear-gradient(135deg,#667eea,#764ba2);">📚</div>
          @endif
          @if($course->is_bestseller)
            <span style="position:absolute;top:12px;{{ app()->getLocale()==='ar'?'right':'left' }}:12px;background:#f59e0b;color:white;font-size:11px;font-weight:700;padding:4px 10px;border-radius:6px;">{{ __('front.courses_bestseller') }}</span>
          @elseif($course->is_trending)
            <span style="position:absolute;top:12px;{{ app()->getLocale()==='ar'?'right':'left' }}:12px;background:var(--red);color:white;font-size:11px;font-weight:700;padding:4px 10px;border-radius:6px;">{{ __('front.courses_trending') }}</span>
          @endif
          @if($course->is_free)
            <span style="position:absolute;top:12px;{{ app()->getLocale()==='ar'?'left':'right' }}:12px;background:#10b981;color:white;font-size:11px;font-weight:700;padding:4px 10px;border-radius:6px;">{{ __('front.courses_free') }}</span>
          @endif
        </a>
        {{-- Body --}}
        <div style="padding:20px;flex:1;display:flex;flex-direction:column;">
          <div style="font-size:11px;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
            {{ app()->getLocale() === 'ar' ? ($course->category->name_ar ?? '') : ($course->category->name_en ?? $course->category->name_ar ?? '') }}
          </div>
          <a href="{{ route('courses.show', $course->id) }}" style="text-decoration:none;">
            <h3 style="font-size:15px;font-weight:800;color:var(--navy);line-height:1.35;margin:0 0 8px;">{{ $course->title }}</h3>
          </a>
          <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0 0 12px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $course->description }}</p>
          @if($course->teacher)
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">
            <a href="{{ route('teachers.show', $course->teacher->id) }}" style="color:var(--navy);font-weight:600;text-decoration:none;">{{ $course->teacher->name }}</a>
          </div>
          @endif
          <div style="display:flex;gap:12px;font-size:12px;color:var(--text-muted);margin-bottom:14px;">
            <span>⭐ {{ number_format($course->average_rating,1) }}</span>
            <span>👥 {{ $course->total_students }}</span>
            @if($course->duration_hours)<span>⏱ {{ $course->duration_hours }}{{ __('front.course_hours_unit') }}</span>@endif
          </div>
          {{-- Price + CTA --}}
          <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
            <div>
              @if($course->is_free || $course->price == 0)
                <span style="font-size:18px;font-weight:800;color:#10b981;">{{ __('front.courses_free') }}</span>
              @else
                <span style="font-size:18px;font-weight:800;color:var(--navy);">{{ $course->price }} {{ __('front.courses_jod') }}</span>
                @if($course->old_price > $course->price)
                  <span style="font-size:13px;color:#94a3b8;text-decoration:line-through;margin-{{ app()->getLocale()==='ar'?'right':'left' }}:6px;">{{ $course->old_price }}</span>
                @endif
              @endif
            </div>
            <div style="display:flex;gap:8px;">
              <a href="{{ route('courses.show', $course->id) }}" style="padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;color:var(--blue);border:1.5px solid var(--blue);text-decoration:none;">{{ __('front.teachers_view') }}</a>
              @if(!$course->is_free)
              <form method="POST" action="{{ route('cart.add', $course->id) }}" style="display:inline;">
                @csrf
                <button type="submit" style="padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;background:var(--blue);color:white;border:none;cursor:pointer;">🛒</button>
              </form>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($courses->hasPages())
    <div style="margin-top:48px;display:flex;justify-content:center;gap:8px;flex-wrap:wrap;">
      @if($courses->onFirstPage())
        <span style="padding:10px 16px;border-radius:9px;background:#f1f5f9;color:#94a3b8;font-size:14px;">‹</span>
      @else
        <a href="{{ $courses->previousPageUrl() }}" style="padding:10px 16px;border-radius:9px;background:white;color:var(--navy);font-size:14px;text-decoration:none;border:1px solid #e2e8f0;">‹</a>
      @endif
      @foreach($courses->getUrlRange(1,$courses->lastPage()) as $page => $url)
        <a href="{{ $url }}" style="padding:10px 16px;border-radius:9px;font-size:14px;text-decoration:none;{{ $page==$courses->currentPage() ? 'background:var(--blue);color:white;font-weight:700;' : 'background:white;color:var(--navy);border:1px solid #e2e8f0;' }}">{{ $page }}</a>
      @endforeach
      @if($courses->hasMorePages())
        <a href="{{ $courses->nextPageUrl() }}" style="padding:10px 16px;border-radius:9px;background:white;color:var(--navy);font-size:14px;text-decoration:none;border:1px solid #e2e8f0;">›</a>
      @else
        <span style="padding:10px 16px;border-radius:9px;background:#f1f5f9;color:#94a3b8;font-size:14px;">›</span>
      @endif
    </div>
    @endif
    @endif

  </div>
</section>

@endsection
