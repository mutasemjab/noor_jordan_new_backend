<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>طباعة أرقام البطاقات</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #fff; color: #111; font-size: 13px; direction: rtl; }

  .print-header { text-align: center; padding: 16px; border-bottom: 2px solid #111; margin-bottom: 16px; }
  .print-header h1 { font-size: 20px; font-weight: 700; }
  .print-header p  { font-size: 12px; color: #555; margin-top: 4px; }

  .group-title { background: #1e293b; color: #fff; padding: 6px 12px; font-weight: 600; font-size: 13px; margin: 14px 0 8px; }

  .cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; padding: 0 8px; }

  .card-item {
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px 12px;
    background: #fff;
    page-break-inside: avoid;
  }
  .card-item .num {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #1e293b;
    word-break: break-all;
  }
  .card-item .meta {
    font-size: 10px;
    color: #666;
    margin-top: 4px;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }
  .pill { padding: 1px 6px; border-radius: 20px; font-size: 10px; }
  .pill-active   { background: #dcfce7; color: #166534; }
  .pill-inactive { background: #f3f4f6; color: #6b7280; }
  .pill-used     { background: #fee2e2; color: #991b1b; }
  .pill-free     { background: #e0f2fe; color: #0369a1; }
  .pill-sold     { background: #fef9c3; color: #854d0e; }

  .summary { display: flex; gap: 24px; justify-content: center; padding: 10px; border-top: 1px solid #ddd; margin-top: 16px; font-size: 12px; }
  .summary span { color: #555; }
  .summary strong { color: #111; }

  .no-results { text-align: center; padding: 40px; color: #888; font-size: 16px; }

  @media print {
    .no-print { display: none !important; }
    @page { size: A4; margin: 12mm; }
    .card-item { break-inside: avoid; }
  }
</style>
</head>
<body>

<div class="print-header">
  <h1>أرقام بطاقات التفعيل</h1>
  <p>{{ now()->format('Y/m/d H:i') }} — إجمالي: {{ $cardNumbers->count() }} رقم</p>
</div>

<div class="no-print" style="text-align:center;margin-bottom:16px">
  <button onclick="window.print()" style="padding:8px 24px;background:#1e293b;color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer">
    🖨️ طباعة
  </button>
  <button onclick="window.close()" style="padding:8px 16px;background:#e5e7eb;color:#111;border:none;border-radius:6px;font-size:14px;cursor:pointer;margin-right:8px">
    إغلاق
  </button>
</div>

@if($cardNumbers->isEmpty())
  <div class="no-results">لا توجد أرقام بطاقات تطابق الفلتر المحدد</div>
@else

  @php
    $grouped = $cardNumbers->groupBy(fn($cn) => $cn->card->name_ar ?? $cn->card->name_en ?? 'بدون بطاقة');
  @endphp

  @foreach($grouped as $cardName => $numbers)
    <div class="group-title">
      {{ $cardName }}
      <span style="font-weight:400;font-size:11px;opacity:.8">({{ $numbers->count() }} رقم)</span>
    </div>

    <div class="cards-grid">
      @foreach($numbers as $cn)
      <div class="card-item">
        <div class="num">{{ $cn->number }}</div>
        <div class="meta">
          <span class="pill {{ $cn->activate === 1 ? 'pill-active' : 'pill-inactive' }}">
            {{ $cn->activate === 1 ? 'مفعّل' : 'غير مفعّل' }}
          </span>
          <span class="pill {{ $cn->status === 2 ? 'pill-free' : 'pill-used' }}">
            {{ $cn->status === 2 ? 'غير مستخدم' : 'مستخدم' }}
          </span>
          <span class="pill {{ $cn->sell === 2 ? 'pill-free' : 'pill-sold' }}">
            {{ $cn->sell === 2 ? 'غير مباع' : 'مباع' }}
          </span>
        </div>
        @if($cn->assignedUser)
        <div style="font-size:10px;color:#555;margin-top:4px">{{ $cn->assignedUser->name }}</div>
        @endif
      </div>
      @endforeach
    </div>
  @endforeach

  <div class="summary">
    <span>إجمالي الأرقام: <strong>{{ $cardNumbers->count() }}</strong></span>
    <span>غير مستخدمة: <strong>{{ $cardNumbers->where('status', 2)->count() }}</strong></span>
    <span>مستخدمة: <strong>{{ $cardNumbers->where('status', 1)->count() }}</strong></span>
    <span>مفعّلة: <strong>{{ $cardNumbers->where('activate', 1)->count() }}</strong></span>
  </div>

@endif

<script>
  // Auto-print on open (comment out if you want the button only)
  // window.onload = () => window.print();
</script>

</body>
</html>
