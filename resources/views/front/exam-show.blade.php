@extends('layouts.front')
@section('title', $exam->title . ' — ' . __('front.site_name'))

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,var(--navy) 0%,#1a1040 100%);padding:100px 5% 56px;">
  <div style="max-width:900px;margin:0 auto;">
    <a href="{{ route('exams.index') }}" style="color:rgba(255,255,255,0.6);font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;">
      ← {{ __('front.nav_exams') }}
    </a>

    @php
      $typeColor = match($exam->exam_type) {
        'previous_year' => ['bg'=>'#fef3c7','text'=>'#92400e','badge'=>'📜'],
        'mock'          => ['bg'=>'#dbeafe','text'=>'#1e40af','badge'=>'📝'],
        'unit'          => ['bg'=>'#ede9fe','text'=>'#5b21b6','badge'=>'📖'],
        'final'         => ['bg'=>'#d1fae5','text'=>'#065f46','badge'=>'🏆'],
        default         => ['bg'=>'#f1f5f9','text'=>'#475569','badge'=>'📋'],
      };
    @endphp

    <span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.9);font-size:12px;font-weight:700;padding:5px 14px;border-radius:8px;display:inline-block;margin-bottom:14px;">
      {{ $typeColor['badge'] }} {{ __('front.exams_type_'.(in_array($exam->exam_type,['mock','unit','final']) ? $exam->exam_type : 'mock')) }}
    </span>
    <h1 style="color:white;font-size:clamp(24px,4vw,38px);font-weight:900;margin:0 0 14px;line-height:1.25;">{{ $exam->title }}</h1>
    @if($exam->description)
    <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.8;max-width:640px;margin:0 0 24px;">{{ $exam->description }}</p>
    @endif

    <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:13px;color:rgba(255,255,255,0.65);">
      @if($exam->total_questions)<span>❓ {{ $exam->total_questions }} {{ __('front.exams_questions') }}</span>@endif
      @if($exam->duration_minutes)<span>⏱ {{ $exam->duration_minutes }} {{ __('front.exams_minutes') }}</span>@endif
      @if($exam->total_attempts)<span>👥 {{ __('front.exams_attempts_count',['n'=>$exam->total_attempts]) }}</span>@endif
      @if($exam->average_success_rate)<span>✅ {{ $exam->average_success_rate }}%</span>@endif
      @if($exam->subject)<span>📚 {{ app()->getLocale()==='ar' ? $exam->subject->name_ar : ($exam->subject->name_en??$exam->subject->name_ar) }}</span>@endif
    </div>
  </div>
</section>

{{-- Body --}}
<section style="padding:48px 5%;background:var(--bg-soft);">
  <div style="max-width:900px;margin:0 auto;display:grid;grid-template-columns:1fr 280px;gap:32px;align-items:start;" class="exam-show-grid">

    {{-- Main --}}
    <div>
      {{-- Instructions --}}
      <div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 14px rgba(0,0,0,0.06);margin-bottom:24px;">
        <h2 style="font-size:17px;font-weight:800;color:var(--navy);margin-bottom:16px;">📋 {{ app()->getLocale()==='ar'?'تعليمات الامتحان':'Exam Instructions' }}</h2>
        <ul style="padding-{{ app()->getLocale()==='ar'?'right':'left' }}:20px;color:var(--text-muted);line-height:2;font-size:14px;">
          @if($exam->total_questions)
          <li>{{ app()->getLocale()==='ar' ? 'يتكون الامتحان من '.$exam->total_questions.' سؤالاً.' : 'The exam consists of '.$exam->total_questions.' questions.' }}</li>
          @endif
          @if($exam->duration_minutes)
          <li>{{ app()->getLocale()==='ar' ? 'المدة الزمنية: '.$exam->duration_minutes.' دقيقة.' : 'Time limit: '.$exam->duration_minutes.' minutes.' }}</li>
          @endif
          <li>{{ app()->getLocale()==='ar' ? 'تأكد من الإجابة على جميع الأسئلة قبل تسليم الامتحان.' : 'Make sure to answer all questions before submitting.' }}</li>
          <li>{{ app()->getLocale()==='ar' ? 'لا يمكن العودة إلى الأسئلة بعد الانتقال إلى التالي.' : 'You cannot go back to previous questions.' }}</li>
          <li>{{ app()->getLocale()==='ar' ? 'ستظهر نتيجتك فور الانتهاء.' : 'Your result appears immediately after submission.' }}</li>
        </ul>
      </div>

      {{-- Course link --}}
      @if($exam->course)
      <div style="background:white;border-radius:16px;padding:20px;box-shadow:0 2px 14px rgba(0,0,0,0.06);margin-bottom:24px;display:flex;gap:14px;align-items:center;">
        <span style="font-size:28px;">📚</span>
        <div>
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:3px;">{{ app()->getLocale()==='ar'?'الدورة المرتبطة':'Related Course' }}</div>
          <a href="{{ route('courses.show', $exam->course->id) }}" style="font-size:14px;font-weight:700;color:var(--blue);text-decoration:none;">{{ $exam->course->title }}</a>
        </div>
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div style="position:sticky;top:100px;">
      <div style="background:white;border-radius:18px;padding:28px;box-shadow:0 4px 24px rgba(0,0,0,0.1);text-align:center;">
        <div style="font-size:48px;margin-bottom:12px;">🎯</div>
        <div style="font-size:16px;font-weight:800;color:var(--navy);margin-bottom:6px;">{{ app()->getLocale()==='ar'?'هل أنت مستعد؟':'Are You Ready?' }}</div>
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:24px;line-height:1.6;">{{ app()->getLocale()==='ar'?'اضغط ابدأ حين تكون جاهزاً — سيبدأ العداد فوراً.':'Press Start when ready — the timer begins immediately.' }}</div>

        @auth('student')
          <a href="{{ route('exams.take', $exam->id) }}" class="btn-primary" style="display:flex;justify-content:center;padding:14px;text-decoration:none;font-size:15px;">
            🚀 {{ __('front.exams_take_btn') }}
          </a>
        @else
          <a href="{{ route('student.login') }}" class="btn-primary" style="display:flex;justify-content:center;padding:14px;text-decoration:none;font-size:15px;">
            {{ app()->getLocale()==='ar'?'سجّل دخولك للبدء':'Login to Start' }}
          </a>
          <p style="font-size:12px;color:var(--text-muted);margin-top:10px;">{{ app()->getLocale()==='ar'?'تحتاج إلى حساب للبدء بالامتحان.':'You need an account to take the exam.' }}</p>
        @endauth

        @if($exam->total_questions || $exam->duration_minutes)
        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-around;text-align:center;">
          @if($exam->total_questions)
          <div>
            <div style="font-size:20px;font-weight:900;color:var(--navy);">{{ $exam->total_questions }}</div>
            <div style="font-size:11px;color:var(--text-muted);">{{ __('front.exams_questions') }}</div>
          </div>
          @endif
          @if($exam->duration_minutes)
          <div>
            <div style="font-size:20px;font-weight:900;color:var(--navy);">{{ $exam->duration_minutes }}</div>
            <div style="font-size:11px;color:var(--text-muted);">{{ __('front.exams_minutes') }}</div>
          </div>
          @endif
          @if($exam->average_success_rate)
          <div>
            <div style="font-size:20px;font-weight:900;color:#10b981;">{{ $exam->average_success_rate }}%</div>
            <div style="font-size:11px;color:var(--text-muted);">{{ app()->getLocale()==='ar'?'نجاح':'Pass Rate' }}</div>
          </div>
          @endif
        </div>
        @endif
      </div>
    </div>

  </div>
</section>

@endsection

@push('styles')
<style>
@media(max-width:800px){ .exam-show-grid{ grid-template-columns:1fr !important; } }
</style>
@endpush
