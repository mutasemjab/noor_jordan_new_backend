@extends('layouts.front')
@section('title', __('front.exams_page_title') . ' — ' . __('front.site_name'))

@section('content')

{{-- ── HERO ── --}}
<section style="background:linear-gradient(135deg,var(--navy) 0%,#1a1040 100%);padding:100px 5% 60px;">
  <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr auto;gap:48px;align-items:center;flex-wrap:wrap;" class="exams-hero-grid">
    <div>
      <div style="background:rgba(255,255,255,0.1);display:inline-block;padding:6px 18px;border-radius:20px;font-size:13px;color:rgba(255,255,255,0.8);font-weight:600;margin-bottom:16px;">{{ __('front.exams_page_badge') }}</div>
      <h1 style="color:white;font-size:clamp(28px,4vw,46px);font-weight:900;margin:0 0 14px;">{{ __('front.exams_page_title') }}</h1>
      <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.8;max-width:540px;margin:0 0 28px;">{{ __('front.exams_page_desc') }}</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('exams.index') }}" class="btn-primary" style="padding:12px 24px;text-decoration:none;">{{ __('front.exams_cta_start') }}</a>
        <a href="{{ route('exams.index') }}?type=previous_year" style="padding:12px 24px;border:1.5px solid rgba(255,255,255,0.35);border-radius:12px;color:white;text-decoration:none;font-weight:600;font-size:14px;">{{ __('front.exams_cta_prev') }}</a>
      </div>
    </div>

    {{-- Leaderboard mini panel --}}
    @if($leaderboard->count())
    <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:18px;padding:24px;min-width:240px;">
      <div style="color:rgba(255,255,255,0.8);font-size:13px;font-weight:700;margin-bottom:16px;">{{ __('front.exams_leaderboard') }}</div>
      <div style="color:rgba(255,255,255,0.5);font-size:11px;margin-bottom:12px;">{{ __('front.exams_week_label') }}</div>
      @foreach($leaderboard->take(3) as $i => $attempt)
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <span style="width:24px;height:24px;border-radius:50%;background:{{ ['#f59e0b','#94a3b8','#cd7c3d'][$i] }};color:white;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;">{{ $i+1 }}</span>
        <span style="color:white;font-size:13px;font-weight:600;flex:1;">{{ $attempt->student->name ?? '---' }}</span>
        <span style="color:#10b981;font-size:13px;font-weight:800;">{{ $attempt->percentage }}%</span>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</section>

{{-- ── FILTER BAR ── --}}
<div style="background:white;border-bottom:1px solid #e2e8f0;padding:16px 5%;position:sticky;top:0;z-index:100;">
  <div style="max-width:1200px;margin:0 auto;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <span style="font-size:14px;font-weight:700;color:var(--navy);">{{ __('front.exams_tag') }}:</span>
    <a href="{{ route('exams.index') }}" style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;{{ !request('type') ? 'background:var(--navy);color:white;' : 'background:#f1f5f9;color:var(--text-muted);' }}">{{ __('front.exams_filter_all') }}</a>
    <a href="{{ route('exams.index') }}?type=mock" style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;{{ request('type')==='mock' ? 'background:var(--blue);color:white;' : 'background:#f1f5f9;color:var(--text-muted);' }}">{{ __('front.exams_type_mock') }}</a>
    <a href="{{ route('exams.index') }}?type=previous_year" style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;{{ request('type')==='previous_year' ? 'background:var(--red);color:white;' : 'background:#f1f5f9;color:var(--text-muted);' }}">{{ __('front.exams_tab_solutions') }}</a>
    <a href="{{ route('exams.index') }}?type=unit" style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;{{ request('type')==='unit' ? 'background:#8b5cf6;color:white;' : 'background:#f1f5f9;color:var(--text-muted);' }}">{{ __('front.exams_type_unit') }}</a>
    <a href="{{ route('exams.index') }}?type=final" style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;{{ request('type')==='final' ? 'background:#10b981;color:white;' : 'background:#f1f5f9;color:var(--text-muted);' }}">{{ __('front.exams_type_final') }}</a>
  </div>
</div>

{{-- ── EXAM GRID ── --}}
<section style="padding:48px 5%;background:var(--bg-soft);min-height:50vh;">
  <div style="max-width:1200px;margin:0 auto;">

    @if($exams->isEmpty())
    <div style="text-align:center;padding:80px 0;color:var(--text-muted);">
      <div style="font-size:48px;margin-bottom:16px;">📋</div>
      <p style="font-size:16px;">{{ __('front.exams_no_exams') }}</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;">
      @foreach($exams as $exam)
      @php
        $typeColor = match($exam->exam_type) {
          'previous_year' => ['bg'=>'#fef3c7','text'=>'#92400e','badge'=>'📜'],
          'mock'          => ['bg'=>'#dbeafe','text'=>'#1e40af','badge'=>'📝'],
          'unit'          => ['bg'=>'#ede9fe','text'=>'#5b21b6','badge'=>'📖'],
          'final'         => ['bg'=>'#d1fae5','text'=>'#065f46','badge'=>'🏆'],
          default         => ['bg'=>'#f1f5f9','text'=>'#475569','badge'=>'📋'],
        };
      @endphp
      <div style="background:white;border-radius:18px;padding:24px;box-shadow:0 2px 16px rgba(0,0,0,0.07);display:flex;flex-direction:column;gap:12px;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 32px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow='0 2px 16px rgba(0,0,0,0.07)'">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
          <div>
            <span style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['text'] }};font-size:11px;font-weight:700;padding:4px 10px;border-radius:6px;display:inline-block;margin-bottom:10px;">{{ $typeColor['badge'] }} {{ __('front.exams_type_'.(in_array($exam->exam_type,['mock','unit','final']) ? $exam->exam_type : 'mock')) }}</span>
            <h3 style="font-size:15px;font-weight:800;color:var(--navy);margin:0;line-height:1.35;">{{ $exam->title }}</h3>
          </div>
        </div>

        @if($exam->description)
        <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $exam->description }}</p>
        @endif

        <div style="display:flex;gap:16px;font-size:12px;color:var(--text-muted);flex-wrap:wrap;">
          @if($exam->total_questions)<span>❓ {{ $exam->total_questions }} {{ __('front.exams_questions') }}</span>@endif
          @if($exam->duration_minutes)<span>⏱ {{ $exam->duration_minutes }} {{ __('front.exams_minutes') }}</span>@endif
          @if($exam->total_attempts)<span>👥 {{ __('front.exams_attempts_count',['n'=>$exam->total_attempts]) }}</span>@endif
          @if($exam->average_success_rate)<span>✅ {{ $exam->average_success_rate }}%</span>@endif
        </div>

        @if($exam->subject)
        <div style="font-size:12px;color:var(--text-muted);">
          📚 {{ app()->getLocale()==='ar' ? $exam->subject->name_ar : ($exam->subject->name_en??$exam->subject->name_ar) }}
          @if(isset($exam->academic_year)) · {{ $exam->academic_year }}@endif
        </div>
        @endif

        <div style="margin-top:auto;padding-top:12px;border-top:1px solid #f1f5f9;">
          @auth('student')
            <a href="{{ route('exams.show', $exam->id) }}" class="btn-primary" style="display:flex;justify-content:center;padding:10px;text-decoration:none;font-size:13px;">{{ __('front.exams_take_btn') }}</a>
          @else
            <a href="{{ route('exams.show', $exam->id) }}" style="display:flex;justify-content:center;padding:10px;border-radius:10px;background:#f8fafc;color:var(--navy);font-size:13px;font-weight:700;text-decoration:none;border:1px solid #e2e8f0;">{{ __('front.exams_take_btn') }} →</a>
          @endauth
        </div>
      </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($exams->hasPages())
    <div style="margin-top:48px;display:flex;justify-content:center;gap:8px;flex-wrap:wrap;">
      @if($exams->onFirstPage())
        <span style="padding:10px 16px;border-radius:9px;background:#f1f5f9;color:#94a3b8;font-size:14px;">‹</span>
      @else
        <a href="{{ $exams->previousPageUrl() }}" style="padding:10px 16px;border-radius:9px;background:white;color:var(--navy);font-size:14px;text-decoration:none;border:1px solid #e2e8f0;">‹</a>
      @endif
      @foreach($exams->getUrlRange(1,$exams->lastPage()) as $page => $url)
        <a href="{{ $url }}" style="padding:10px 16px;border-radius:9px;font-size:14px;text-decoration:none;{{ $page==$exams->currentPage() ? 'background:var(--navy);color:white;font-weight:700;' : 'background:white;color:var(--navy);border:1px solid #e2e8f0;' }}">{{ $page }}</a>
      @endforeach
      @if($exams->hasMorePages())
        <a href="{{ $exams->nextPageUrl() }}" style="padding:10px 16px;border-radius:9px;background:white;color:var(--navy);font-size:14px;text-decoration:none;border:1px solid #e2e8f0;">›</a>
      @else
        <span style="padding:10px 16px;border-radius:9px;background:#f1f5f9;color:#94a3b8;font-size:14px;">›</span>
      @endif
    </div>
    @endif
    @endif

  </div>
</section>

@endsection

@push('styles')
<style>
@media(max-width:900px){ .exams-hero-grid{ grid-template-columns:1fr !important; } }
</style>
@endpush
