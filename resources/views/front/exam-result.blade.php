@extends('layouts.front')
@section('title', (app()->getLocale()==='ar'?'نتيجة الامتحان':'Exam Result') . ' — ' . __('front.site_name'))

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,var(--navy),{{ $attempt->is_passed ? '#065f46' : '#7f1d1d' }});padding:80px 5% 48px;">
  <div style="max-width:780px;margin:0 auto;text-align:center;">
    <div style="font-size:64px;margin-bottom:16px;">{{ $attempt->is_passed ? '🏆' : '💪' }}</div>
    <h1 style="color:white;font-size:clamp(24px,4vw,40px);font-weight:900;margin:0 0 10px;">
      {{ $attempt->is_passed
          ? (app()->getLocale()==='ar' ? 'أحسنت! لقد نجحت' : 'Congratulations! You Passed')
          : (app()->getLocale()==='ar' ? 'لم تنجح هذه المرة' : 'Keep Trying — Not Passed') }}
    </h1>
    <p style="color:rgba(255,255,255,0.7);font-size:15px;">{{ $exam->title }}</p>

    {{-- Score circle --}}
    <div style="margin:32px auto 0;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.1);border:6px solid rgba(255,255,255,0.3);display:flex;flex-direction:column;align-items:center;justify-content:center;">
      <div style="font-size:42px;font-weight:900;color:white;">{{ $attempt->percentage }}%</div>
      <div style="font-size:12px;color:rgba(255,255,255,0.7);">{{ $attempt->score }}/{{ $attempt->total_marks }}</div>
    </div>
  </div>
</section>

{{-- Stats bar --}}
<section style="background:white;border-bottom:1px solid #e2e8f0;padding:24px 5%;">
  <div style="max-width:780px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center;">
    @php
      $answeredCount = $attempt->answers->count();
      $correctCount  = $attempt->answers->where('is_correct', true)->count();
      $wrongCount    = $attempt->answers->where('is_correct', false)->whereNotNull('selected_option_id')->count();
      $unanswered    = $attempt->answers->whereNull('selected_option_id')->count();
      $timeTaken     = $attempt->time_taken_seconds ? gmdate('i:s', $attempt->time_taken_seconds) : '—';
    @endphp
    <div>
      <div style="font-size:28px;font-weight:900;color:#10b981;">{{ $correctCount }}</div>
      <div style="font-size:12px;color:var(--text-muted);">{{ app()->getLocale()==='ar'?'إجابات صحيحة':'Correct' }}</div>
    </div>
    <div>
      <div style="font-size:28px;font-weight:900;color:#ef4444;">{{ $wrongCount }}</div>
      <div style="font-size:12px;color:var(--text-muted);">{{ app()->getLocale()==='ar'?'إجابات خاطئة':'Wrong' }}</div>
    </div>
    <div>
      <div style="font-size:28px;font-weight:900;color:#94a3b8;">{{ $unanswered }}</div>
      <div style="font-size:12px;color:var(--text-muted);">{{ app()->getLocale()==='ar'?'بدون إجابة':'Unanswered' }}</div>
    </div>
    <div>
      <div style="font-size:28px;font-weight:900;color:var(--navy);">{{ $timeTaken }}</div>
      <div style="font-size:12px;color:var(--text-muted);">{{ app()->getLocale()==='ar'?'الوقت':'Time' }}</div>
    </div>
  </div>
</section>

{{-- Actions --}}
<section style="padding:28px 5%;background:var(--bg-soft);">
  <div style="max-width:780px;margin:0 auto;display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ route('exams.take', $exam->id) }}" style="padding:12px 24px;border-radius:12px;background:var(--blue);color:white;font-size:14px;font-weight:700;text-decoration:none;">
      🔁 {{ app()->getLocale()==='ar'?'إعادة المحاولة':'Retry Exam' }}
    </a>
    <a href="{{ route('exams.index') }}" style="padding:12px 24px;border-radius:12px;border:2px solid #e2e8f0;background:white;color:var(--navy);font-size:14px;font-weight:700;text-decoration:none;">
      ← {{ app()->getLocale()==='ar'?'جميع الامتحانات':'All Exams' }}
    </a>
  </div>
</section>

{{-- Question Review --}}
<section style="padding:8px 5% 60px;background:var(--bg-soft);">
  <div style="max-width:780px;margin:0 auto;">
    <h2 style="font-size:20px;font-weight:800;color:var(--navy);margin-bottom:20px;">
      {{ app()->getLocale()==='ar'?'مراجعة الأسئلة':'Question Review' }}
    </h2>

    @foreach($attempt->answers->sortBy('question_id') as $i => $ans)
    @php
      $question      = $ans->question;
      $selectedOpt   = $ans->selectedOption;
      $correctOpt    = $question ? $question->options->firstWhere('is_correct', true) : null;
      $isCorrect     = $ans->is_correct;
      $isUnanswered  = is_null($ans->selected_option_id);
    @endphp
    @if(!$question) @continue @endif

    <div style="background:white;border-radius:16px;padding:24px;margin-bottom:16px;border-{{ app()->getLocale()==='ar'?'right':'left' }}:5px solid {{ $isCorrect ? '#10b981' : ($isUnanswered ? '#94a3b8' : '#ef4444') }};box-shadow:0 2px 12px rgba(0,0,0,0.06);">
      {{-- Q header --}}
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <span style="font-size:12px;font-weight:700;color:var(--text-muted);">
          {{ app()->getLocale()==='ar'?'سؤال':'Q' }} {{ $i+1 }}
        </span>
        <span style="font-size:12px;font-weight:700;padding:4px 10px;border-radius:6px;
          {{ $isCorrect ? 'background:#d1fae5;color:#065f46;' : ($isUnanswered ? 'background:#f1f5f9;color:#64748b;' : 'background:#fef2f2;color:#991b1b;') }}">
          {{ $isCorrect
              ? (app()->getLocale()==='ar'?'✅ صحيح':'✅ Correct')
              : ($isUnanswered
                  ? (app()->getLocale()==='ar'?'— لم تُجب':'— Unanswered')
                  : (app()->getLocale()==='ar'?'❌ خطأ':'❌ Wrong')) }}
          · {{ $ans->marks_earned }}/{{ $question->marks }}
        </span>
      </div>

      {{-- Question text --}}
      <p style="font-size:15px;font-weight:700;color:var(--navy);margin:0 0 18px;line-height:1.6;">
        {{ $question->question_text }}
      </p>

      {{-- Options review --}}
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
        @foreach($question->options as $opt)
        @php
          $isSelected = $selectedOpt && $selectedOpt->id === $opt->id;
          $isCorrectOpt = $opt->is_correct;
          if ($isCorrectOpt && $isSelected) {
            $bg='#d1fae5'; $border='#10b981'; $icon='✅';
          } elseif ($isCorrectOpt) {
            $bg='#d1fae5'; $border='#10b981'; $icon='✅';
          } elseif ($isSelected) {
            $bg='#fef2f2'; $border='#ef4444'; $icon='❌';
          } else {
            $bg='#f8fafc'; $border='#e2e8f0'; $icon='';
          }
        @endphp
        <div style="padding:10px 14px;border-radius:10px;background:{{ $bg }};border:1.5px solid {{ $border }};font-size:13px;font-weight:{{ ($isSelected||$isCorrectOpt)?'700':'500' }};color:var(--navy);display:flex;align-items:center;gap:8px;">
          <span style="width:18px;font-size:14px;text-align:center;">{{ $icon }}</span>
          <span>{{ $opt->option_text }}</span>
          @if($isSelected && !$isCorrectOpt)
          <span style="font-size:11px;color:#991b1b;margin-{{ app()->getLocale()==='ar'?'right':'left' }}:auto;">{{ app()->getLocale()==='ar'?'إجابتك':'Your answer' }}</span>
          @endif
        </div>
        @endforeach
      </div>

      {{-- Explanation --}}
      @if($question->explanation)
      <div style="background:#fffbeb;border-radius:10px;padding:14px;border-{{ app()->getLocale()==='ar'?'right':'left' }}:3px solid #f59e0b;">
        <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:4px;">💡 {{ app()->getLocale()==='ar'?'الشرح':'Explanation' }}</div>
        <div style="font-size:13px;color:#78350f;line-height:1.7;">{{ $question->explanation }}</div>
      </div>
      @endif
    </div>
    @endforeach
  </div>
</section>

@endsection
