@extends('layouts.front')
@section('title', $teacher->name . ' — ' . __('front.site_name'))

@section('content')

{{-- ── HERO ── --}}
<section style="background:linear-gradient(135deg,var(--navy) 0%,#1a2a4a 100%);padding:100px 5% 60px;min-height:340px;display:flex;align-items:flex-end;">
  <div style="max-width:1200px;margin:0 auto;width:100%;display:flex;align-items:flex-end;gap:40px;flex-wrap:wrap;">
    <div style="width:120px;height:120px;border-radius:50%;border:4px solid rgba(255,255,255,0.2);overflow:hidden;flex-shrink:0;background:var(--blue);">
      @if($teacher->avatar)
        <img src="{{ asset('assets/uploads/teachers/'.$teacher->avatar) }}" alt="{{ $teacher->name }}" style="width:100%;height:100%;object-fit:cover;">
      @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;font-weight:900;color:white;">{{ mb_substr($teacher->name,0,1) }}</div>
      @endif
    </div>
    <div style="flex:1;min-width:200px;">
      <div style="color:rgba(255,255,255,0.6);font-size:13px;margin-bottom:6px;">{{ __('front.teachers_tag') }}</div>
      <h1 style="color:white;font-size:clamp(26px,4vw,40px);font-weight:900;margin:0 0 8px;">{{ $teacher->name }}</h1>
      <div style="display:flex;gap:24px;flex-wrap:wrap;">
        <div style="text-align:center;">
          <div style="color:white;font-size:22px;font-weight:800;">{{ $teacher->total_students ?? 0 }}</div>
          <div style="color:rgba(255,255,255,0.6);font-size:12px;">{{ __('front.teachers_students') }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── BODY ── --}}
<section style="padding:60px 5%;background:var(--bg-soft);">
  <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 320px;gap:40px;align-items:start;" class="teacher-profile-grid">

    {{-- LEFT: Subjects --}}
    <div>
      @if($teacher->subjects->count())
      <div style="background:white;border-radius:16px;padding:32px;margin-bottom:32px;box-shadow:0 2px 20px rgba(0,0,0,0.06);">
        <h2 style="font-size:20px;font-weight:800;color:var(--navy);margin-bottom:16px;">{{ __('front.teacher_info_subjects') }}</h2>
        <div style="display:flex;flex-wrap:wrap;gap:10px;">
          @foreach($teacher->subjects as $subject)
          <span style="background:var(--bg-soft);border:1px solid #e2e8f0;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;color:var(--navy);">
            {{ $subject->icon ?? '' }} {{ app()->getLocale() === 'ar' ? $subject->name_ar : ($subject->name_en ?? $subject->name_ar) }}
          </span>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- RIGHT: Info Card --}}
    <div>
      <div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 20px rgba(0,0,0,0.06);position:sticky;top:100px;">
        <h3 style="font-size:16px;font-weight:800;color:var(--navy);margin-bottom:20px;">{{ __('front.teacher_info_details') }}</h3>

        @if($teacher->phone)
        <div style="display:flex;gap:12px;margin-bottom:16px;align-items:flex-start;">
          <span style="font-size:18px;">📞</span>
          <div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:2px;">{{ __('messages.phone_label') }}</div>
            <div style="font-size:14px;font-weight:600;color:var(--navy);">{{ $teacher->phone }}</div>
          </div>
        </div>
        @endif

        <a href="{{ route('teachers.index') }}" class="btn-primary" style="display:flex;width:100%;justify-content:center;text-align:center;">
          <span>{{ __('front.teachers_section_t') }}</span>
        </a>
      </div>
    </div>

  </div>
</section>

@endsection

@push('styles')
<style>
@media(max-width:900px){
  .teacher-profile-grid{ grid-template-columns:1fr !important; }
}
</style>
@endpush
