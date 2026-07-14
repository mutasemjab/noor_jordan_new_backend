@extends('layouts.front')
@section('title', $course->title . ' — ' . __('front.site_name'))

@section('content')

{{-- ── HERO ── --}}
<section style="background:linear-gradient(135deg,var(--navy) 0%,#0f1e35 100%);padding:100px 5% 56px;">
  <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 340px;gap:48px;align-items:start;" class="course-detail-grid">
    <div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
        <span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.8);padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;">
          {{ app()->getLocale()==='ar' ? ($course->category->name_ar??'') : ($course->category->name_en??$course->category->name_ar??'') }}
        </span>
        @if($course->is_bestseller)<span style="background:#f59e0b;color:white;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:700;">{{ __('front.courses_bestseller') }}</span>@endif
        @if($course->is_trending)<span style="background:var(--red);color:white;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:700;">{{ __('front.courses_trending') }}</span>@endif
      </div>
      <h1 style="color:white;font-size:clamp(22px,3.5vw,38px);font-weight:900;line-height:1.25;margin:0 0 16px;">{{ $course->title }}</h1>
      <p style="color:rgba(255,255,255,0.75);font-size:15px;line-height:1.8;margin:0 0 24px;max-width:640px;">{{ $course->description }}</p>
      <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:13px;color:rgba(255,255,255,0.7);">
        <span>⭐ {{ number_format($course->average_rating,1) }}</span>
        <span>👥 {{ $course->total_students }} {{ __('front.teachers_students') }}</span>
        @if($course->duration_hours)<span>⏱ {{ $course->duration_hours }} {{ __('front.course_hours') }}</span>@endif
        @if($course->total_videos)<span>🎬 {{ $course->total_videos }} {{ __('front.course_videos') }}</span>@endif
        @if($course->teacher)
          <span>👨‍🏫 <a href="{{ route('teachers.show',$course->teacher->id) }}" style="color:rgba(255,255,255,0.9);font-weight:600;text-decoration:none;">{{ $course->teacher->name }}</a></span>
        @endif
      </div>
    </div>

    {{-- ── SIDEBAR ── --}}
    <div class="course-sidebar-card" style="background:white;border-radius:20px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:sticky;top:90px;">
      @if($course->thumbnail)
        <img src="{{ asset('assets/uploads/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;border-radius:12px;margin-bottom:20px;object-fit:cover;max-height:180px;">
      @endif

      @if($isEnrolled)
        {{-- Already enrolled --}}
        <div style="background:#d1fae5;border-radius:12px;padding:16px;text-align:center;margin-bottom:16px;">
          <div style="font-size:28px;margin-bottom:6px;">✅</div>
          <div style="font-size:14px;font-weight:800;color:#065f46;">{{ app()->getLocale()==='ar'?'أنت مسجّل في هذه الدورة':'You are enrolled in this course' }}</div>
        </div>
        <a href="#curriculum" class="btn-primary" style="display:flex;justify-content:center;padding:14px;text-decoration:none;font-size:14px;">
          {{ __('front.course_enroll_btn') }}
        </a>

      @elseif($course->is_free || $course->price == 0)
        {{-- Free course --}}
        <div style="font-size:28px;font-weight:900;color:#10b981;margin-bottom:16px;">{{ __('front.courses_free') }}</div>
        <a href="#curriculum" class="btn-primary" style="display:flex;justify-content:center;padding:14px;margin-bottom:12px;text-decoration:none;">
          {{ __('front.course_enroll_btn') }}
        </a>

      @else
        {{-- Card activation --}}
        @if(session('activation_success') && session('activated_course') == $course->id)
        <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:12px;padding:14px;margin-bottom:16px;display:flex;gap:8px;align-items:center;font-size:13px;font-weight:700;color:#065f46;">
          <span>✅</span> {{ session('activation_success') }}
        </div>
        @endif

        @if(session('activation_error') && session('error_course') == $course->id)
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px;margin-bottom:16px;display:flex;gap:8px;align-items:center;font-size:13px;font-weight:700;color:#991b1b;">
          <span>⚠️</span> {{ session('activation_error') }}
        </div>
        @endif

        <div style="margin-bottom:16px;">
          <span style="font-size:28px;font-weight:900;color:var(--navy);">{{ $course->price }} {{ __('front.courses_jod') }}</span>
          @if($course->old_price > $course->price)
            <span style="font-size:15px;color:#94a3b8;text-decoration:line-through;margin-{{ app()->getLocale()==='ar'?'right':'left' }}:8px;">{{ $course->old_price }}</span>
          @endif
        </div>

        @auth('student')
        <form method="POST" action="{{ route('courses.activate', $course->id) }}">
          @csrf
          <label style="font-size:12px;font-weight:700;color:var(--text-muted);display:block;margin-bottom:8px;">🎴 {{ __('front.card_number_label') }}</label>
          <input
            type="text"
            name="card_number"
            placeholder="{{ __('front.card_number_ph') }}"
            autocomplete="off"
            style="width:100%;padding:13px 16px;border-radius:10px;border:2px solid #e2e8f0;font-size:15px;letter-spacing:2px;outline:none;box-sizing:border-box;margin-bottom:12px;font-family:monospace;text-align:center;transition:border .2s;"
            onfocus="this.style.borderColor='var(--blue)'"
            onblur="this.style.borderColor='#e2e8f0'"
          >
          <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:14px;font-weight:800;">
            ✅ {{ __('front.activate_now') }}
          </button>
        </form>
        @else
        <a href="{{ route('student.login') }}" class="btn-primary" style="display:flex;justify-content:center;padding:14px;text-decoration:none;font-size:14px;">
          {{ __('front.nav_login') }} {{ app()->getLocale()==='ar'?'للتفعيل':'to Activate' }}
        </a>
        @endauth
      @endif

      <div style="border-top:1px solid #f1f5f9;margin:16px 0;padding-top:16px;">
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:10px;font-size:13px;color:var(--text-muted);">
          <span>♾️</span><span>{{ __('front.course_sidebar_access') }}</span>
        </div>
        <div style="display:flex;gap:10px;align-items:center;font-size:13px;color:var(--text-muted);">
          <span>🏆</span><span>{{ __('front.course_sidebar_cert') }}</span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── TABS ── --}}
<section style="padding:0 5%;background:var(--bg-soft);" id="curriculum">
  <div style="max-width:1200px;margin:0 auto;">

    <div style="display:flex;border-bottom:2px solid #e2e8f0;background:white;border-radius:16px 16px 0 0;overflow:hidden;margin-top:32px;">
      <button onclick="showTab('tab-curriculum',this)" class="ctab" style="padding:16px 28px;font-size:14px;font-weight:700;border:none;cursor:pointer;background:transparent;color:var(--blue);border-bottom:2px solid var(--blue);margin-bottom:-2px;">{{ __('front.course_tab_curriculum') }}</button>
      <button onclick="showTab('tab-about',this)" class="ctab" style="padding:16px 28px;font-size:14px;font-weight:700;border:none;cursor:pointer;background:transparent;color:var(--text-muted);">{{ __('front.course_tab_about') }}</button>
    </div>

    <div id="tab-curriculum" class="ctab-panel" style="background:white;border-radius:0 0 16px 16px;padding:32px;margin-bottom:48px;">
      @forelse($course->units as $unit)
      @php
        $unitExamCount = $unit->lessons->filter(fn($l) => $lessonExams->has($l->id))->count()
                       + ($unitEndExams->has($unit->id) ? 1 : 0);
        $unitItemCount = $unit->lessons->count() + $unitExamCount;
      @endphp
      <div style="margin-bottom:20px;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
        <div style="background:#f8fafc;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;" onclick="toggleUnit(this)">
          <div>
            <div style="font-size:15px;font-weight:800;color:var(--navy);">{{ $unit->title }}</div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $unitItemCount }} {{ __('front.course_unit_lessons') }}</div>
          </div>
          <span class="unit-arrow" style="color:var(--text-muted);transition:transform .2s;display:inline-block;">▼</span>
        </div>
        <div class="unit-body" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">
          <div style="padding:0 20px;">
            @foreach($unit->lessons as $lesson)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f1f5f9;">
              <span style="font-size:18px;flex-shrink:0;">{{ ($isEnrolled || $lesson->is_free) ? '▶️' : '🔒' }}</span>
              <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;color:var(--navy);">{{ $lesson->title }}</div>
                @if($lesson->duration_minutes)
                <div style="font-size:11px;color:var(--text-muted);">{{ $lesson->duration_minutes }} {{ __('front.exams_minutes') }}</div>
                @endif
              </div>
              @if($lesson->is_free)
              <span style="font-size:11px;font-weight:700;color:#10b981;background:#d1fae5;padding:3px 8px;border-radius:5px;flex-shrink:0;">{{ __('front.course_free_lesson') }}</span>
              @endif
            </div>
            {{-- Exam placed after this specific lesson --}}
            @if($lessonExams->has($lesson->id))
            @php $le = $lessonExams[$lesson->id]; @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f1f5f9;background:#fffbeb;">
              <span style="font-size:18px;flex-shrink:0;">{{ $isEnrolled ? '📝' : '🔒' }}</span>
              <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;color:#92400e;">{{ $le->title }}</div>
                <div style="font-size:11px;color:#b45309;">{{ $le->total_questions ?? '?' }} {{ __('front.questions') }} · {{ $le->duration_minutes }} {{ __('front.exams_minutes') }}</div>
              </div>
              <span style="font-size:11px;font-weight:700;color:#92400e;background:#fef3c7;padding:3px 8px;border-radius:5px;flex-shrink:0;">{{ __('front.exam_label') }}</span>
            </div>
            @endif
            @endforeach
            {{-- Exam at end of unit --}}
            @if($unitEndExams->has($unit->id))
            @php $ue = $unitEndExams[$unit->id]; @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f1f5f9;background:#eff6ff;">
              <span style="font-size:18px;flex-shrink:0;">{{ $isEnrolled ? '📝' : '🔒' }}</span>
              <div style="flex:1;">
                <div style="font-size:13px;font-weight:700;color:#1e40af;">{{ $ue->title }}</div>
                <div style="font-size:11px;color:#3b82f6;">{{ $ue->total_questions ?? '?' }} {{ __('front.questions') }} · {{ $ue->duration_minutes }} {{ __('front.exams_minutes') }}</div>
              </div>
              <span style="font-size:11px;font-weight:700;color:#1e40af;background:#dbeafe;padding:3px 8px;border-radius:5px;flex-shrink:0;">{{ __('front.unit_exam') }}</span>
            </div>
            @endif
          </div>
        </div>
      </div>
      @empty
      <p style="color:var(--text-muted);text-align:center;padding:32px 0;">{{ __('front.course_no_units') }}</p>
      @endforelse

      {{-- Course-level exams (not tied to any unit/lesson) --}}
      @foreach($courseExams as $ce)
      <div style="margin-bottom:16px;border:2px solid #ddd6fe;border-radius:14px;overflow:hidden;background:#faf5ff;">
        <div style="padding:16px 20px;display:flex;align-items:center;gap:14px;">
          <span style="font-size:24px;">{{ $isEnrolled ? '📝' : '🔒' }}</span>
          <div style="flex:1;">
            <div style="font-size:15px;font-weight:800;color:#5b21b6;">{{ $ce->title }}</div>
            <div style="font-size:12px;color:#7c3aed;margin-top:2px;">{{ $ce->total_questions ?? '?' }} {{ __('front.questions') }} · {{ $ce->duration_minutes }} {{ __('front.exams_minutes') }}</div>
          </div>
          <span style="font-size:11px;font-weight:700;color:#5b21b6;background:#ede9fe;padding:4px 10px;border-radius:6px;">{{ __('front.final_exam') }}</span>
        </div>
      </div>
      @endforeach
    </div>

    <div id="tab-about" class="ctab-panel" style="display:none;background:white;border-radius:0 0 16px 16px;padding:32px;margin-bottom:48px;">
      @if($course->what_you_learn_ar || $course->what_you_learn_en)
      <h3 style="font-size:18px;font-weight:800;color:var(--navy);margin-bottom:16px;">{{ app()->getLocale()==='ar'?'ماذا ستتعلم؟':'What You\'ll Learn' }}</h3>
      <div style="color:var(--text-muted);line-height:1.9;white-space:pre-line;margin-bottom:28px;">{{ app()->getLocale()==='ar' ? $course->what_you_learn_ar : ($course->what_you_learn_en??$course->what_you_learn_ar) }}</div>
      @endif
      @if($course->requirements_ar || $course->requirements_en)
      <h3 style="font-size:18px;font-weight:800;color:var(--navy);margin-bottom:16px;">{{ app()->getLocale()==='ar'?'المتطلبات':'Requirements' }}</h3>
      <div style="color:var(--text-muted);line-height:1.9;white-space:pre-line;">{{ app()->getLocale()==='ar' ? $course->requirements_ar : ($course->requirements_en??$course->requirements_ar) }}</div>
      @endif
      @if(!$course->what_you_learn_ar && !$course->requirements_ar)
      <p style="color:var(--text-muted);padding:24px 0;">{{ $course->description }}</p>
      @endif
    </div>

    @if($relatedCourses->count())
    <div style="margin-bottom:60px;">
      <h2 style="font-size:22px;font-weight:800;color:var(--navy);margin-bottom:24px;">{{ __('front.course_related') }}</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
        @foreach($relatedCourses as $rel)
        <a href="{{ route('courses.show',$rel->id) }}" style="background:white;border-radius:14px;overflow:hidden;text-decoration:none;display:flex;gap:14px;padding:14px;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
          <div style="width:72px;height:56px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#e2e8f0;">
            @if($rel->thumbnail)<img src="{{ asset('assets/uploads/'.$rel->thumbnail) }}" alt="{{ $rel->title }}" style="width:100%;height:100%;object-fit:cover;">
            @else<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">📚</div>@endif
          </div>
          <div>
            <div style="font-size:13px;font-weight:700;color:var(--navy);line-height:1.3;margin-bottom:4px;">{{ $rel->title }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $rel->price > 0 ? $rel->price.' '.__('front.courses_jod') : __('front.courses_free') }}</div>
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</section>

@endsection

@push('styles')
<style>
@media(max-width:900px){ .course-detail-grid{ grid-template-columns:1fr !important; } .course-sidebar-card{ position:static !important; } }
</style>
@endpush

@push('scripts')
<script>
function showTab(id,btn){
  document.querySelectorAll('.ctab-panel').forEach(p=>p.style.display='none');
  document.querySelectorAll('.ctab').forEach(b=>{b.style.color='var(--text-muted)';b.style.borderBottom='none';b.style.marginBottom='0';});
  document.getElementById(id).style.display='block';
  btn.style.color='var(--blue)'; btn.style.borderBottom='2px solid var(--blue)'; btn.style.marginBottom='-2px';
}
function toggleUnit(el){
  var body=el.nextElementSibling, arrow=el.querySelector('.unit-arrow');
  if(body.style.maxHeight==='0px'||!body.style.maxHeight){ body.style.maxHeight='2000px'; arrow.style.transform='rotate(180deg)'; }
  else{ body.style.maxHeight='0px'; arrow.style.transform=''; }
}
document.addEventListener('DOMContentLoaded',function(){
  var first=document.querySelector('.unit-body');
  if(first){ first.style.maxHeight='2000px'; }
  var firstArr=document.querySelector('.unit-arrow');
  if(firstArr) firstArr.style.transform='rotate(180deg)';
});
</script>
@endpush
