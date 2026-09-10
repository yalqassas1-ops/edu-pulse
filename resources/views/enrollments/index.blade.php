<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيلات الطلاب - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
        .badge-custom { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid">
        <!-- Header Section -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                
                <!-- Page Title -->
                <div class="text-center text-md-start">
                    <h3 class="fw-bold mb-1 text-dark">
                        تسجيلات الشُعب الدراسية <i class="fa-solid fa-user-check text-secondary ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتتبع تسجيلات الطلاب في الشُعب والدورات</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('enrollments.create') }}" class="btn btn-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-plus me-1"></i> تسجيل طالب جديد
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-gauge me-1"></i> لوحة التحكم
                    </a>
                </div>

            </div>
        </div>

        <!-- Notifications -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Data Table -->
        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3 text-start">اسم الطالب</th>
                            <th class="py-3">الشعبة والدورة</th>
                            <th class="py-3">تاريخ التسجيل</th>
                            <th class="py-3">الحالة</th>
                            <th class="py-3">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                                <td class="text-start fw-bold text-dark">{{ $enrollment->student->name ?? 'طالب محذوف' }}</td>
                                <td>
                                    @if($enrollment->courseClass)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-layer-group me-1"></i>
                                            الشعبة: {{ $enrollment->courseClass->class_number ?? $enrollment->courseClass->class_code ?? $enrollment->courseClass->name ?? $enrollment->course_class_id }}
                                        </span>
                                        <small class="text-muted d-block mt-1 fw-semibold">
                                            {{ $enrollment->courseClass->course->title ?? 'دورة غير محددة' }}
                                        </small>
                                    @elseif(isset($enrollment->course))
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-book me-1"></i> تسجيل مباشر
                                        </span>
                                        <small class="text-dark d-block mt-1 fw-bold">
                                            {{ $enrollment->course->title }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-circle-question me-1"></i> الشعبة: -
                                        </span>
                                        <small class="text-danger d-block mt-1 fw-semibold">
                                            غير مرتبطة بشعبة
                                        </small>
                                    @endif
                                </td>
                                <td class="text-muted small fw-semibold">
                                    {{ $enrollment->enrollment_date ?? $enrollment->enrolled_at ?? date('Y-m-d') }}
                                </td>
                                <td>
                                    @if($enrollment->status == 'active' || $enrollment->status == 'نشط')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-circle-check me-1"></i> نشط
                                        </span>
                                    @elseif($enrollment->status == 'completed' || $enrollment->status == 'مكتمل')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-check-double me-1"></i> مكتمل
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-user-xmark me-1"></i> منسحب
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('enrollments.edit', $enrollment->id) }}" class="btn btn-sm btn-outline-warning btn-custom" title="تعديل">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('enrollments.destroy', $enrollment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من إلغاء التسجيل؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-custom" title="إلغاء التسجيل">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-3 d-block text-secondary"></i>
                                    لا توجد تسجيلات حالية.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>