<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المدفوعات والأقساط - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
        .stat-card { border-right: 4px solid #198754; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid">
        <!-- Header -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        سجل المدفوعات والأقساط <i class="fa-solid fa-receipt text-success ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">متابعة دفعات الطلاب ورسوم الدورات وإصدار السندات</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-custom" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="fa-solid fa-plus me-1"></i> تسجيل دفعة جديدة
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom">
                        <i class="fa-solid fa-gauge me-1"></i> لوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Stat Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card card-custom stat-card p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small">إجمالي التحصيلات المقبوضة</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">${{ number_format($totalAmount, 2) }}</h4>
                        </div>
                        <div class="bg-success-subtle p-3 rounded-circle text-success">
                            <i class="fa-solid fa-wallet fa-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="card card-custom overflow-hidden mb-4">
            <div class="p-4 border-bottom bg-white">
                <form method="GET" action="{{ route('payments.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="بحث باسم الطالب أو رقم السند..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="student_id" class="form-select">
                            <option value="">-- فلترة حسب الطالب --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-custom w-100">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> بحث
                        </button>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-custom">إلغاء</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>رقم السند</th>
                            <th class="text-start">اسم الطالب</th>
                            <th>الشعبة / الدورة</th>
                            <th>المبلغ المدفوع</th>
                            <th>المبلغ المتبقي</th>
                            <th>التاريخ</th>
                            <th>طريقة الدفع</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="fw-bold text-secondary">#{{ $payment->receipt_number ?? $payment->id }}</td>
                                <td class="text-start fw-bold text-dark">{{ $payment->student->name ?? 'غير محدد' }}</td>
                                
                                <!-- إظهار اسم الدورة أولاً ثم رقم الشعبة بتنسيق صحيح -->
                                <td>
                                    @if($payment->courseClass)
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <span class="fw-bold">{{ $payment->courseClass->course->title ?? '' }}</span>
                                            <span class="badge bg-secondary">شعبة {{ $payment->courseClass->class_number ?? $payment->courseClass->name ?? $payment->courseClass->id }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">عام / قسط عام</span>
                                    @endif
                                </td>

                                <td class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</td>

                                <!-- حساب المتبقي الصحيح بناءً على مجموع المدفوعات لسعر الدورة -->
                                @php
                                    $coursePrice = $payment->courseClass->course->price ?? 0;
                                    
                                    $totalPaidByStudent = \App\Models\Payment::where('student_id', $payment->student_id)
                                        ->where('course_class_id', $payment->course_class_id)
                                        ->sum('amount');

                                    $remaining = $coursePrice > 0 ? ($coursePrice - $totalPaidByStudent) : 0;
                                @endphp
                                <td>
                                    @if($coursePrice > 0)
                                        @if($remaining <= 0)
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">مكتمل</span>
                                        @else
                                            <span class="fw-bold text-danger">${{ number_format($remaining, 2) }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">غير محدد</span>
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $payment->payment_method == 'cash' ? 'نقداً' : ($payment->payment_method == 'card' ? 'بطاقة' : 'تحويل') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('payments.print', $payment->id) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="طباعة سند">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف السند؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">
                                    <i class="fa-solid fa-receipt fa-2x mb-3 d-block text-secondary"></i>
                                    لا يوجد سندات قبض مسجلة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-light border-top">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: إضافة دفعة -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle me-1"></i> تسجيل دفعة/سند قبض</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر الطالب <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="" disabled selected>-- اختر الطالب --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر الشعبة (اختياري)</label>
                            <select name="course_class_id" class="form-select">
                                <option value="">-- دفعة عامة --</option>
                                @foreach($courseClasses as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->course->title ?? 'دورة' }} - شعبة: {{ $class->class_number ?? $class->name ?? $class->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">المبلغ المقبوض <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">طريقة الدفع</label>
                                <select name="payment_method" class="form-select">
                                    <option value="cash">نقداً (Cash)</option>
                                    <option value="card">بطاقة (Card)</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">رقم السند</label>
                                <input type="text" name="receipt_number" class="form-control" placeholder="REC-102">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات أو بيان الدفعة</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="دفعة أولى من رسوم الدورة..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-custom" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success btn-custom"><i class="fa-solid fa-floppy-disk me-1"></i> حفظ السند</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>