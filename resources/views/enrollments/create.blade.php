<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل طالب جديد - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #334155; }
        .form-control, .form-select { border-radius: 8px; padding: 0.6rem 0.75rem; border: 1px solid #cbd5e1; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid" style="max-width: 900px;">
        <!-- Header -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold m-0 text-dark">
                        تسجيل طالب جديد <i class="fa-solid fa-user-plus text-success ms-1"></i>
                    </h2>
                    <small class="text-muted">إضافة تسجيل جديد لطالب في شعبة دراسية</small>
                </div>
                <div>
                    <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary fw-bold">
                        <i class="fa-solid fa-arrow-right me-1"></i> رجوع للتسجيلات
                    </a>
                </div>
            </div>
        </div>

        <!-- إظهار رسائل الأخطاء إن وجدت -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card card-custom p-4">
            <form action="{{ route('enrollments.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- اختيار الطالب -->
                    <div class="col-md-6">
                        <label for="student_id" class="form-label">
                            <i class="fa-solid fa-user-graduate text-primary me-1"></i> الطالب <span class="text-danger">*</span>
                        </label>
                        <select name="student_id" id="student_id" class="form-select" required>
                            <option value="" selected disabled>اختر الطالب...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name ?? ($student->first_name ?? 'طالب #'.$student->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اختيار الشعبة الدراسية -->
                    <div class="col-md-6">
                        <label for="course_class_id" class="form-label">
                            <i class="fa-solid fa-chalkboard-user text-primary me-1"></i> الشعبة الدراسية <span class="text-danger">*</span>
                        </label>
                        <select name="course_class_id" id="course_class_id" class="form-select" required>
                            <option value="" selected disabled>اختر الشعبة الدراسية...</option>
                            @foreach($courseClasses as $class)
                                <option value="{{ $class->id }}" {{ old('course_class_id') == $class->id ? 'selected' : '' }}>
                                    الشعبة {{ $class->class_number }} - ({{ $class->course->title ?? 'دورة غير محددة' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تاريخ التسجيل -->
                    <div class="col-md-6">
                        <label for="enrollment_date" class="form-label">
                            <i class="fa-solid fa-calendar-days text-primary me-1"></i> تاريخ التسجيل
                        </label>
                        <input type="date" name="enrollment_date" id="enrollment_date" class="form-control" value="{{ old('enrollment_date', date('Y-m-d')) }}">
                    </div>

                    <!-- حالة التسجيل -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">
                            <i class="fa-solid fa-toggle-on text-primary me-1"></i> حالة التسجيل <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط (Active)</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل (Completed)</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغى (Cancelled)</option>
                        </select>
                    </div>
                </div>

                <!-- أزرار الإرسال والإلغاء -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('enrollments.index') }}" class="btn btn-light fw-bold">إلغاء</a>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-check me-1"></i> حفظ التسجيل
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>