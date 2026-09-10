<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الدورة - {{ $course->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 600px;">
        <div class="card card-custom p-4 bg-white">
            <!-- الهيدر وعنوان الدورة -->
            <div class="text-center mb-4">
                <span class="badge bg-primary fs-6 mb-2">{{ $course->category->name ?? 'غير محدد' }}</span>
                <h3 class="fw-bold text-dark m-0">{{ $course->title }}</h3>
            </div>

            <hr>

            <!-- تفاصيل الدورة -->
            <div class="row g-3 my-2">
                <div class="col-6">
                    <div class="p-3 bg-light rounded text-center">
                        <i class="fa-solid fa-user-graduate text-success fs-4 mb-2"></i>
                        <div class="text-muted small">المحاضر</div>
                        <div class="fw-bold text-dark fs-6">{{ $course->teacher->name ?? 'غير محدد' }}</div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 bg-light rounded text-center">
                        <i class="fa-solid fa-clock text-info fs-4 mb-2"></i>
                        <div class="text-muted small">عدد الساعات</div>
                        <div class="fw-bold text-dark fs-6">{{ $course->total_hours }} ساعة</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 bg-light rounded text-center">
                        <i class="fa-solid fa-tag text-warning fs-4 mb-2"></i>
                        <div class="text-muted small">سعر الدورة</div>
                        <div class="fw-bold text-success fs-5">${{ number_format($course->price, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- الأزرار -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('courses.index') }}" class="btn btn-secondary px-4 fw-bold">رجوع للقائمة</a>
                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning text-white px-4 fw-bold">
                    <i class="fa-solid fa-pen me-1"></i> تعديل
                </a>
            </div>
        </div>
    </div>

</body>
</html>