<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إيصال دفع – {{ $payment->receipt_number }}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Cairo', sans-serif;
        background: #f4f6fa;
        color: #1e2333;
        direction: rtl;
    }

    .page {
        max-width: 680px;
        margin: 40px auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 40px rgba(0,0,0,.10);
        overflow: hidden;
    }

    /* Header strip */
    .receipt-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #fff;
        padding: 32px 36px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .receipt-header .brand { font-size: 1.5rem; font-weight: 800; letter-spacing: -.5px; }
    .receipt-header .doc-type { font-size: .85rem; opacity: .85; margin-top: 2px; }
    .receipt-header .rec-number {
        text-align: left;
        font-size: 1.1rem;
        font-weight: 700;
        font-family: monospace;
        background: rgba(255,255,255,.15);
        padding: 8px 16px;
        border-radius: 8px;
    }

    /* Body */
    .receipt-body { padding: 32px 36px; }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 28px;
    }
    .info-item label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 4px;
    }
    .info-item .value {
        font-size: .97rem;
        font-weight: 600;
        color: #111827;
    }

    /* Amount box */
    .amount-box {
        background: #f5f3ff;
        border: 2px solid #ede9fe;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        margin-bottom: 28px;
    }
    .amount-box .label { font-size: .82rem; color: #6b7280; margin-bottom: 6px; }
    .amount-box .amount { font-size: 2.8rem; font-weight: 800; color: #4f46e5; line-height: 1; }
    .amount-box .currency { font-size: 1rem; font-weight: 600; color: #7c3aed; margin-top: 4px; }

    /* Summary table */
    .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
    .summary-table td { padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: .9rem; }
    .summary-table td:last-child { text-align: left; font-weight: 600; }
    .summary-table tr:last-child td { border-bottom: none; }
    .text-green { color: #059669; }
    .text-red   { color: #dc2626; }

    /* Notes */
    .notes-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: .85rem;
        color: #92400e;
        margin-bottom: 28px;
    }
    .notes-box .notes-label { font-weight: 700; margin-bottom: 4px; }

    /* Footer */
    .receipt-footer {
        border-top: 1px solid #f3f4f6;
        padding: 20px 36px;
        text-align: center;
        font-size: .78rem;
        color: #9ca3af;
    }

    /* Print button */
    .print-bar {
        text-align: center;
        padding: 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    .print-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 28px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: .92rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .print-btn:hover { background: #4338ca; }
    .close-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 28px;
        background: #f3f4f6;
        color: #374151;
        border: none;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: .92rem;
        font-weight: 600;
        cursor: pointer;
        margin-inline-start: 10px;
        text-decoration: none;
    }
    .close-btn:hover { background: #e5e7eb; }

    @media print {
        body { background: #fff; }
        .page { margin: 0; box-shadow: none; border-radius: 0; max-width: 100%; }
        .print-bar { display: none; }
        .receipt-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .amount-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
</head>
<body>

<div class="page">
    {{-- Header --}}
    <div class="receipt-header">
        <div>
            <div class="brand">نور الأردن</div>
            <div class="doc-type">إيصال دفع رسمي</div>
        </div>
        <div class="rec-number">{{ $payment->receipt_number }}</div>
    </div>

    {{-- Body --}}
    <div class="receipt-body">

        <div class="info-grid">
            <div class="info-item">
                <label>اسم الطالب</label>
                <div class="value">{{ $payment->contract->student->name }}</div>
            </div>
            <div class="info-item">
                <label>تاريخ الدفع</label>
                <div class="value">{{ $payment->paid_at->format('d / m / Y') }}</div>
            </div>
            <div class="info-item">
                <label>رقم الهاتف</label>
                <div class="value">{{ $payment->contract->student->phone ?: '—' }}</div>
            </div>
            <div class="info-item">
                <label>تاريخ الإصدار</label>
                <div class="value">{{ $payment->created_at->format('d / m / Y  H:i') }}</div>
            </div>
        </div>

        {{-- Amount --}}
        <div class="amount-box">
            <div class="label">المبلغ المدفوع</div>
            <div class="amount">{{ number_format($payment->amount, 2) }}</div>
            <div class="currency">دينار أردني</div>
        </div>

        {{-- Contract summary --}}
        <table class="summary-table">
            <tr>
                <td>إجمالي العقد</td>
                <td>{{ number_format($payment->contract->total_amount, 2) }} د.أ</td>
            </tr>
            <tr>
                <td>إجمالي المدفوع</td>
                <td class="text-green">{{ number_format($payment->contract->paid_amount, 2) }} د.أ</td>
            </tr>
            <tr>
                <td>المتبقي</td>
                <td class="{{ $payment->contract->remaining_amount > 0 ? 'text-red' : 'text-green' }}">
                    {{ number_format($payment->contract->remaining_amount, 2) }} د.أ
                </td>
            </tr>
        </table>

        @if($payment->notes)
        <div class="notes-box">
            <div class="notes-label">ملاحظات</div>
            {{ $payment->notes }}
        </div>
        @endif

    </div>

    {{-- Footer --}}
    <div class="receipt-footer">
        هذا الإيصال وثيقة رسمية صادرة عن نور الأردن &mdash; يُرجى الاحتفاظ به
    </div>

    {{-- Print / Close bar --}}
    <div class="print-bar">
        <button class="print-btn" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            طباعة الإيصال
        </button>
        <a href="{{ route('admin.students.contract', $payment->contract->student->id) }}" class="close-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            العودة للعقد
        </a>
    </div>
</div>

<script>
    // Auto-print when opened in a new tab
    window.addEventListener('load', function () {
        if (window.opener || document.referrer === '') {
            // Only auto-print when opened fresh (from redirect or new tab)
            window.print();
        }
    });
</script>

</body>
</html>
