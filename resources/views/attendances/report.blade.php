<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير حضور واختبار الطلاب - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
        
        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff; padding: 0 !important; }
            .card-custom { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid">
        <!-- Header -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        تقرير حضور وغياب الطلاب <i class="fa-solid fa-chart-pie text-primary ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">عرض المجموع الشامل لأيام الحضور، الغياب، والتأخير لكل طالب</p>
                </div>
                <div class="no-print d-flex gap-2">
                    <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary btn-custom">
                        <i class="fa-solid fa-arrow-right me-1"></i> تسجيل الحضور
                    </a>
                    @if($selectedClassId && count($reportData) > 0)
                        <button onclick="window.print()" class="btn btn-success btn-custom">
                            <i class="fa-solid fa-print me-1"></i> طباعة التقرير
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card card-custom p-4 mb-4 no-print">
            <form method="GET" action="{{ route('attendances.report') }}" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label for="course_class_id" class="form-label fw-bold">اختر الشعبة الدراسية لعرض التقرير <span class="text-danger">*</span></label>
                    <select name="course_class_id" id="course_class_id" class="form-select" required>
                        <option value="" selected disabled>-- اختر الشعبة --</option>
                        @foreach($courseClasses as $class)
                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                شعبة: {{ $class->class_number ?? $class->class_code ?? $class->id }} - الدورة: {{ $class->course->title ?? $class->course->name ?? 'غير محددة' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 btn-custom">
                        <i class="fa-solid fa-chart-column me-1"></i> توليد التقرير
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Table -->
        @if($selectedClassId)
            <div class="card card-custom overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-list-check text-secondary me-2"></i> ملخص حضور الطلاب
                    </h5>
                    <span class="badge bg-primary fs-6 px-3 py-2">عدد الطلاب: {{ count($reportData) }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th class="text-start">اسم الطالب</th>
                                <th>رقم الهاتف</th>
                                <th class="text-success"><i class="fa-solid fa-circle-check me-1"></i> أيام الحضور</th>
                                <th class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> أيام الغياب</th>
                                <th class="text-warning"><i class="fa-solid fa-clock me-1"></i> مرات التأخير</th>
                                <th>إجمالي الأيام المسجلة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $row)
                                <tr>
                                    <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td class="text-start fw-bold text-dark">{{ $row->name }}</td>
                                    <td>{{ $row->phone }}</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-bold fs-6">{{ $row->present_count }}</span></td>
                                    <td><span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 fw-bold fs-6">{{ $row->absent_count }}</span></td>
                                    <td><span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 fw-bold fs-6">{{ $row->late_count }}</span></td>
                                    <td class="fw-bold fs-6">{{ $row->total_days }} يوم</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-muted">
                                        لا يوجد طلاب مسجلون في هذه الشعبة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</body>
</html>