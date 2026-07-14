@extends('layouts.front')
@section('title', $exam->title . ' — ' . __('front.site_name'))

@push('styles')
<style>
*{ box-sizing:border-box; }
body{ background:#f0f4f8; }
.exam-wrap{ display:grid; grid-template-columns:280px 1fr; gap:0; min-height:100vh; }
.exam-sidebar{ background:var(--navy); color:white; padding:24px; position:sticky; top:0; height:100vh; overflow-y:auto; display:flex; flex-direction:column; gap:20px; }
.exam-main{ padding:32px 40px; max-width:820px; margin:0 auto; width:100%; }
.q-card{ background:white; border-radius:18px; padding:32px; box-shadow:0 2px 20px rgba(0,0,0,0.08); margin-bottom:24px; display:none; }
.q-card.active{ display:block; }
.option-label{ display:flex; align-items:center; gap:14px; padding:14px 18px; border-radius:12px; border:2px solid #e2e8f0; cursor:pointer; transition:all .2s; margin-bottom:10px; font-size:15px; color:var(--navy); font-weight:500; }
.option-label:hover{ border-color:var(--blue); background:#eff6ff; }
.option-label input[type=radio]{ display:none; }
.option-label.selected{ border-color:var(--blue); background:#eff6ff; font-weight:700; }
.option-dot{ width:22px; height:22px; border-radius:50%; border:2px solid #cbd5e1; flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:all .2s; }
.option-label.selected .option-dot{ border-color:var(--blue); background:var(--blue); }
.option-label.selected .option-dot::after{ content:''; width:8px; height:8px; border-radius:50%; background:white; display:block; }
.q-nav-dot{ width:34px; height:34px; border-radius:8px; border:none; font-size:12px; font-weight:700; cursor:pointer; transition:all .15s; }
.q-nav-dot.unanswered{ background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7); }
.q-nav-dot.answered{ background:var(--blue); color:white; }
.q-nav-dot.current{ background:white; color:var(--navy); box-shadow:0 0 0 2px var(--blue); }
.timer-danger{ animation:pulse 1s infinite; }
@keyframes pulse{ 0%,100%{ opacity:1; } 50%{ opacity:.6; } }
@media(max-width:900px){
  .exam-wrap{ grid-template-columns:1fr; }
  .exam-sidebar{ position:relative; height:auto; flex-direction:row; flex-wrap:wrap; }
  .exam-main{ padding:20px; }
}
</style>
@endpush

@section('content')
<div class="exam-wrap">

  {{-- ── SIDEBAR ── --}}
  <aside class="exam-sidebar">
    {{-- Timer --}}
    <div style="text-align:center;">
      <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">
        {{ app()->getLocale()==='ar'?'الوقت المتبقي':'Time Remaining' }}
      </div>
      <div id="timer" style="font-size:42px;font-weight:900;color:white;font-family:monospace;letter-spacing:2px;">
        {{ $exam->duration_minutes ? sprintf('%02d:00', $exam->duration_minutes) : '∞' }}
      </div>
    </div>

    {{-- Progress --}}
    <div>
      <div style="display:flex;justify-content:space-between;font-size:12px;color:rgba(255,255,255,0.6);margin-bottom:8px;">
        <span>{{ app()->getLocale()==='ar'?'الإجابات':'Answered' }}</span>
        <span><span id="answered-count">0</span>/{{ $questions->count() }}</span>
      </div>
      <div style="background:rgba(255,255,255,0.15);border-radius:6px;height:6px;">
        <div id="progress-bar" style="background:var(--blue);height:6px;border-radius:6px;width:0%;transition:width .3s;"></div>
      </div>
    </div>

    {{-- Question navigator --}}
    <div>
      <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.5);letter-spacing:1px;text-transform:uppercase;margin-bottom:12px;">
        {{ app()->getLocale()==='ar'?'الأسئلة':'Questions' }}
      </div>
      <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;">
        @foreach($questions as $i => $q)
        <button type="button" class="q-nav-dot {{ $i===0?'current':'unanswered' }}" id="dot-{{ $i }}" onclick="goTo({{ $i }})">{{ $i+1 }}</button>
        @endforeach
      </div>
    </div>

    {{-- Legend --}}
    <div style="font-size:12px;color:rgba(255,255,255,0.5);display:flex;flex-direction:column;gap:6px;margin-top:auto;">
      <div style="display:flex;gap:8px;align-items:center;"><span style="width:14px;height:14px;border-radius:4px;background:rgba(255,255,255,0.1);display:inline-block;"></span>{{ app()->getLocale()==='ar'?'لم تُجب':'Unanswered' }}</div>
      <div style="display:flex;gap:8px;align-items:center;"><span style="width:14px;height:14px;border-radius:4px;background:var(--blue);display:inline-block;"></span>{{ app()->getLocale()==='ar'?'تمت الإجابة':'Answered' }}</div>
    </div>
  </aside>

  {{-- ── MAIN ── --}}
  <main class="exam-main">
    <div style="margin-bottom:24px;">
      <h1 style="font-size:20px;font-weight:800;color:var(--navy);margin:0 0 4px;">{{ $exam->title }}</h1>
      <div style="font-size:13px;color:var(--text-muted);">{{ $questions->count() }} {{ app()->getLocale()==='ar'?'سؤال':'questions' }} · {{ $exam->total_marks ?? $questions->sum('marks') }} {{ app()->getLocale()==='ar'?'درجة':'marks' }}</div>
    </div>

    <form id="exam-form" method="POST" action="{{ route('exams.submit', $exam->id) }}">
      @csrf
      <input type="hidden" name="time_taken_seconds" id="time-taken">

      @foreach($questions as $i => $question)
      <div class="q-card {{ $i===0?'active':'' }}" id="q-{{ $i }}" data-index="{{ $i }}">
        {{-- Question header --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
          <span style="background:#eff6ff;color:var(--blue);font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;">
            {{ app()->getLocale()==='ar'?'سؤال':'Question' }} {{ $i+1 }} / {{ $questions->count() }}
          </span>
          <div style="display:flex;gap:8px;align-items:center;">
            <span style="background:#f0fdf4;color:#065f46;font-size:11px;font-weight:700;padding:4px 10px;border-radius:6px;">
              {{ $question->marks }} {{ app()->getLocale()==='ar'?'درجة':'mark(s)' }}
            </span>
            @if($question->difficulty)
            <span style="background:#f8fafc;color:var(--text-muted);font-size:11px;padding:4px 10px;border-radius:6px;">
              {{ match($question->difficulty){ 'easy'=>(app()->getLocale()==='ar'?'سهل':'Easy'), 'hard'=>(app()->getLocale()==='ar'?'صعب':'Hard'), default=>(app()->getLocale()==='ar'?'متوسط':'Medium') } }}
            </span>
            @endif
          </div>
        </div>

        {{-- Question text --}}
        <p style="font-size:18px;font-weight:700;color:var(--navy);line-height:1.7;margin:0 0 24px;">
          {{ $question->question_text }}
        </p>

        {{-- Options --}}
        <div>
          @if($question->question_type === 'true_false')
            @php $tfOptions = [['id'=>'true','ar'=>'صحيح','en'=>'True'],['id'=>'false','ar'=>'خطأ','en'=>'False']]; @endphp
            @foreach($tfOptions as $tf)
            <label class="option-label" id="lbl-{{ $i }}-{{ $tf['id'] }}" onclick="selectOption({{ $i }}, '{{ $tf['id'] }}', this)">
              <span class="option-dot"></span>
              <span>{{ app()->getLocale()==='ar' ? $tf['ar'] : $tf['en'] }}</span>
              <input type="radio" name="answers[{{ $question->id }}]" value="{{ $tf['id'] }}">
            </label>
            @endforeach

          @else
            {{-- MCQ --}}
            @php $letters = ['أ','ب','ج','د','هـ']; $enLetters = ['A','B','C','D','E']; @endphp
            @foreach($question->options as $j => $option)
            <label class="option-label" id="lbl-{{ $i }}-{{ $option->id }}" onclick="selectOption({{ $i }}, {{ $option->id }}, this)">
              <span class="option-dot"></span>
              <span style="font-size:13px;font-weight:700;color:var(--blue);min-width:20px;">
                {{ app()->getLocale()==='ar' ? ($letters[$j]??($j+1).'.') : ($enLetters[$j]??($j+1).'.') }}
              </span>
              <span>{{ $option->option_text }}</span>
              <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}">
            </label>
            @endforeach
          @endif
        </div>
      </div>
      @endforeach

      {{-- Navigation --}}
      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
        <button type="button" id="btn-prev" onclick="prevQ()" style="padding:12px 28px;border-radius:12px;border:2px solid #e2e8f0;background:white;color:var(--navy);font-size:14px;font-weight:700;cursor:pointer;display:none;">
          {{ app()->getLocale()==='ar'?'← السابق':'← Prev' }}
        </button>
        <span></span>
        <button type="button" id="btn-next" onclick="nextQ()" style="padding:12px 28px;border-radius:12px;background:var(--blue);color:white;border:none;font-size:14px;font-weight:700;cursor:pointer;">
          {{ app()->getLocale()==='ar'?'التالي →':'Next →' }}
        </button>
        <button type="button" id="btn-submit" onclick="confirmSubmit()" style="padding:12px 28px;border-radius:12px;background:#10b981;color:white;border:none;font-size:14px;font-weight:700;cursor:pointer;display:none;">
          ✅ {{ app()->getLocale()==='ar'?'تسليم الامتحان':'Submit Exam' }}
        </button>
      </div>
    </form>
  </main>
</div>

{{-- Confirm submit modal --}}
<div id="submit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:36px;max-width:420px;width:90%;text-align:center;">
    <div style="font-size:48px;margin-bottom:16px;">📋</div>
    <h2 style="font-size:20px;font-weight:800;color:var(--navy);margin-bottom:10px;" id="modal-title">
      {{ app()->getLocale()==='ar'?'تأكيد التسليم':'Confirm Submission' }}
    </h2>
    <p id="modal-msg" style="color:var(--text-muted);font-size:14px;margin-bottom:24px;line-height:1.7;"></p>
    <div style="display:flex;gap:12px;justify-content:center;">
      <button onclick="document.getElementById('submit-modal').style.display='none'" style="padding:12px 24px;border-radius:10px;border:2px solid #e2e8f0;background:white;color:var(--navy);font-weight:700;cursor:pointer;font-size:14px;">
        {{ app()->getLocale()==='ar'?'مراجعة':'Review' }}
      </button>
      <button onclick="doSubmit()" style="padding:12px 24px;border-radius:10px;background:#10b981;color:white;border:none;font-weight:700;cursor:pointer;font-size:14px;">
        {{ app()->getLocale()==='ar'?'تسليم الآن':'Submit Now' }}
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var TOTAL       = {{ $questions->count() }};
var DURATION    = {{ $exam->duration_minutes ? $exam->duration_minutes * 60 : 0 }};
var currentIdx  = 0;
var answered    = {};
var startedAt   = Date.now();
var timerLeft   = DURATION;
var timerEl     = document.getElementById('timer');
var timeTakenEl = document.getElementById('time-taken');
var isAr        = {{ app()->getLocale()==='ar' ? 'true' : 'false' }};

/* ── Timer ── */
if (DURATION > 0) {
  var timerInterval = setInterval(function() {
    timerLeft--;
    if (timerLeft <= 0) { clearInterval(timerInterval); doSubmit(); return; }
    var m = Math.floor(timerLeft / 60);
    var s = timerLeft % 60;
    timerEl.textContent = (m<10?'0':'')+m+':'+(s<10?'0':'')+s;
    if (timerLeft <= 60) { timerEl.classList.add('timer-danger'); timerEl.style.color='#ef4444'; }
    else if (timerLeft <= 300) { timerEl.style.color='#f59e0b'; }
  }, 1000);
}

/* ── Navigation ── */
function goTo(idx) {
  document.getElementById('q-'+currentIdx).classList.remove('active');
  document.getElementById('dot-'+currentIdx).classList.remove('current');
  currentIdx = idx;
  document.getElementById('q-'+currentIdx).classList.add('active');
  updateDot(currentIdx);
  updateNavBtns();
  window.scrollTo({top:0,behavior:'smooth'});
}
function nextQ() { if (currentIdx < TOTAL-1) goTo(currentIdx+1); }
function prevQ() { if (currentIdx > 0) goTo(currentIdx-1); }

function updateNavBtns() {
  document.getElementById('btn-prev').style.display  = currentIdx > 0        ? 'inline-block' : 'none';
  document.getElementById('btn-next').style.display  = currentIdx < TOTAL-1  ? 'inline-block' : 'none';
  document.getElementById('btn-submit').style.display= currentIdx === TOTAL-1 ? 'inline-block' : 'none';
}

function updateDot(idx) {
  var dot = document.getElementById('dot-'+idx);
  dot.classList.remove('unanswered','answered');
  dot.classList.add('current');
  for (var i=0; i<TOTAL; i++) {
    if (i===idx) continue;
    var d = document.getElementById('dot-'+i);
    d.classList.remove('current');
    d.classList.add(answered[i] ? 'answered' : 'unanswered');
  }
}

/* ── Option selection ── */
function selectOption(qIdx, optId, labelEl) {
  var qCard = document.getElementById('q-'+qIdx);
  qCard.querySelectorAll('.option-label').forEach(function(l){ l.classList.remove('selected'); });
  labelEl.classList.add('selected');
  labelEl.querySelector('input[type=radio]').checked = true;
  answered[qIdx] = optId;
  updateProgress();
  var dot = document.getElementById('dot-'+qIdx);
  if (dot.classList.contains('current')) { dot.classList.remove('unanswered'); }
  else { dot.classList.remove('unanswered'); dot.classList.add('answered'); }
}

function updateProgress() {
  var count = Object.keys(answered).length;
  document.getElementById('answered-count').textContent = count;
  document.getElementById('progress-bar').style.width = (count/TOTAL*100)+'%';
}

/* ── Submit ── */
function confirmSubmit() {
  var unanswered = TOTAL - Object.keys(answered).length;
  var msg = isAr
    ? 'أجبت على '+ Object.keys(answered).length +' من '+ TOTAL +' أسئلة.' + (unanswered > 0 ? ' تبقّى '+ unanswered +' سؤال بدون إجابة.' : ' جميع الأسئلة مُجابة.')
    : 'You answered '+ Object.keys(answered).length +' of '+ TOTAL +' questions.' + (unanswered > 0 ? ' '+ unanswered +' unanswered.' : ' All answered.');
  document.getElementById('modal-msg').textContent = msg;
  document.getElementById('submit-modal').style.display = 'flex';
}

function doSubmit() {
  document.getElementById('submit-modal').style.display = 'none';
  var elapsed = Math.round((Date.now() - startedAt) / 1000);
  if (DURATION > 0) elapsed = DURATION - timerLeft;
  timeTakenEl.value = elapsed;
  document.getElementById('exam-form').submit();
}

/* ── Keyboard nav ── */
document.addEventListener('keydown', function(e) {
  if (e.key==='ArrowRight' && !isAr) nextQ();
  if (e.key==='ArrowLeft'  && !isAr) prevQ();
  if (e.key==='ArrowRight' &&  isAr) prevQ();
  if (e.key==='ArrowLeft'  &&  isAr) nextQ();
});

updateNavBtns();
</script>
@endpush
