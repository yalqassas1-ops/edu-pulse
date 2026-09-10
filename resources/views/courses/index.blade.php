<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الدورات التدريبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table th { background-color: #1e293b; color: white; text-align: center; }
        .table td { vertical-align: middle; text-align: center; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid">
        <!-- Header -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <!-- العنوان والوصف (اليمين) -->
                <div class="text-start">
                    <h2 class="fw-bold m-0 text-dark">
                        إدارة الدورات التدريبية <i class="fa-solid fa-graduation-cap text-primary ms-1"></i>
                    </h2>
                    <small class="text-muted">عرض وتعديل كافة الدورات المسجلة في النظام</small>
                </div>

                <!-- الأزرار الأساسية فقط (اليسار) -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('courses.create') }}" class="btn btn-primary fw-bold">
                        <i class="fa-solid fa-plus me-1"></i> إضافة دورة جديدة
                    </a>
                    <a href="/dashboard" class="btn btn-outline-secondary fw-bold">
                        <i class="fa-solid fa-gauge me-1"></i> لوحة التحكم
                    </a>
                </div>

            </div>
        </div>

        <!-- التنبيهات -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Table -->
        <div class="card card-custom p-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان الدورة</th>
                            <th>التصنيف</th>
                            <th>المحاضر</th>
                            <th>السعر</th>
                            <th>عدد الساعات</th>
                            <th>التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td class="fw-bold">{{ $loop->iteration }}</td>
                                <td class="fw-bold text-dark">
                                    <i class="fa-solid fa-book text-secondary me-1"></i> {{ $course->title }}
                                </td>
                                <td>
                                    <span class="badge p-2" style="background-color: #f3e8ff; color: #7e22ce;">
                                        <i class="fa-solid fa-layer-group me-1"></i> {{ $course->category->name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td>
                                    @if($course->teacher)
                                        <span class="badge p-2" style="background-color: #dcfce7; color: #15803d;">
                                            <i class="fa-solid fa-user-check me-1"></i> {{ $course->teacher->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted p-2">
                                            <i class="fa-solid fa-user-xmark me-1"></i> غير محدد
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">${{ number_format($course->price, 2) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark p-2 border">
                                        <i class="fa-regular fa-clock me-1"></i> {{ $course->total_hours }} ساعة
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-info" title="عرض">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الدورة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">لا توجد دورات تدريبية مضافة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- سكربت Bootstrap الصحيح لتشغيل زر إغلاق التنبيهات -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>