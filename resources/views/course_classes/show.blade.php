<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الشعبة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 900px;">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">
                    تفاصيل الشعبة: {{ $courseClass->class_number }} <i class="fa-solid fa-circle-info ms-2" style="color: #7c3aed;"></i>
                </h4>
                <a href="{{ route('course-classes.index') }}" class="btn btn-outline-secondary btn-custom">
                    <i class="fa-solid fa-arrow-right me-1"></i> عودة للشُعب
                </a>
            </div>
        </div>

        <!-- Class Details Card -->
        <div class="card card-custom p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="text-muted mb-1">اسم الدورة التدريبية</p>
                    <h5 class="fw-bold text-dark">{{ $courseClass->course->title ?? 'غير محدد' }}</h5>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">المحاضر</p>
                    <h5 class="fw-bold text-dark">{{ $courseClass->teacher->name ?? 'غير محدد' }}</h5>
                </div>
                <hr>
                <div class="col-md-4">
                    <p class="text-muted mb-1">القاعة الدراسية</p>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $courseClass->classRoom->name ?? 'غير محدد' }}</span>
                </div>
                <div class="col-md-4">
                    <p class="text-muted mb-1">الأيام</p>
                    @if(is_array($courseClass->days))
                        @foreach($courseClass->days as $day)
                            <span class="badge bg-primary me-1">{{ $day }}</span>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-4">
                    <p class="text-muted mb-1">التوقيت</p>
                    <p class="fw-bold text-secondary mb-0">
                        {{ $courseClass->start_time }} - {{ $courseClass->end_time }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Enrolled Students Table -->
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">الطلاب المسجلون في الشعبة ({{ $courseClass->enrollments->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الطالب</th>
                            <th>تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courseClass->enrollments as $enrollment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $enrollment->student->name ?? 'غير محدد' }}</td>
                                <td>{{ $enrollment->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted py-4">لا يوجد طلاب مسجلون في هذه الشعبة حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>