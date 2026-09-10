<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سند قبض #{{ $payment->receipt_number ?? $payment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background: #fff; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 20px; }
        .receipt-box { border: 2px solid #333; padding: 30px; border-radius: 10px; max-width: 800px; margin: 0 auto; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2">طباعة السند</button>
        <button onclick="window.close()" class="btn btn-secondary px-4">إغلاق</button>
    </div>

    <div class="receipt-box">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">مركز EduPulse التعليمي</h2>
                <p class="text-muted mb-0 small">سند قبض ماليات ورسوم دراسية</p>
            </div>
            <div class="text-end">
                <h4 class="text-success fw-bold">سند قبض</h4>
                <p class="mb-0"><strong>الرقم:</strong> #{{ $payment->receipt_number ?? $payment->id }}</p>
                <p class="mb-0"><strong>التاريخ:</strong> {{ $payment->payment_date }}</p>
            </div>
        </div>

        <div class="row mb-4 fs-5">
            <div class="col-12 mb-3">
                <strong>استلمنا من السيد/ة:</strong> <span class="border-bottom border-secondary d-inline-block px-3 fw-bold">{{ $payment->student->name ?? '---' }}</span>
            </div>
            <div class="col-12 mb-3">
                <strong>مبلغاً وقدره:</strong> <span class="badge bg-success fs-5 px-3">${{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="col-12 mb-3">
                <strong>وذلك عن:</strong> {{ $payment->courseClass->course->title ?? 'رسوم وأقساط عامة' }}
            </div>
            <div class="col-12 mb-3">
                <strong>طريقة الدفع:</strong> {{ $payment->payment_method == 'cash' ? 'نقداً' : 'تحويل/بطاقة' }}
            </div>
            @if($payment->notes)
            <div class="col-12 mb-3">
                <strong>ملاحظات:</strong> {{ $payment->notes }}
            </div>
            @endif
        </div>

        <div class="row pt-5 text-center mt-4">
            <div class="col-6">
                <p class="fw-bold mb-5">توقيع المستلم</p>
                <p>_______________________</p>
            </div>
            <div class="col-6">
                <p class="fw-bold mb-5">ختم المركز</p>
                <p>_______________________</p>
            </div>
        </div>
    </div>

</body>
</html>