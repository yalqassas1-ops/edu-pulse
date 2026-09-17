<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف التسجيلات - EduPulse</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ⚡ استدعاء مكتبات Vite الخاص بالصفحة -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                        أرشيف التسجيلات المحذوفة <i class="fa-solid fa-box-archive text-warning ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">استعادة أو حذف التسجيلات المؤرشفة نهائياً من النظام</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    @if($enrollments->isNotEmpty())
                        <form action="{{ route('enrollments.archive.force-delete-all') }}" method="POST" class="d-inline" onsubmit="return confirm('تحذير شديد: هل أنت متأكد من تفريغ آرشيف التسجيلات بالكامل؟ سيتم حذف جميع التسجيلات المؤرشفة نهائياً ولا يمكن التراجع عن هذا الإجراء إطلاقاً!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-custom shadow-sm">
                                <i class="fa-solid fa-dumpster me-1"></i> تفريغ الأرشيف بالكامل
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('enrollments.index') }}" class="btn btn-primary btn-custom shadow-sm">
                        <i class="fa-solid fa-arrow-right me-1"></i> العودة لقائمة التسجيلات
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-gauge me-1"></i> لوحة التحكم
                    </a>
                </div>

            </div>
        </div>

        <!-- Data Table -->
        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3 text-start">اسم الطالب</th>
                            <th class="py-3">الشعبة والدورة</th>
                            <th class="py-3">تاريخ الأرشفة</th>
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
                                    {{ $enrollment->deleted_at ? $enrollment->deleted_at->format('Y-m-d H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- زر الاستعادة -->
                                        <form action="{{ route('enrollments.restore', $enrollment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل ترغب في استعادة هذا التسجيل؟')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success btn-custom" title="استعادة">
                                                <i class="fa-solid fa-rotate-left me-1"></i> استعادة
                                            </button>
                                        </form>

                                        <!-- زر الحذف النهائي -->
                                        <form action="{{ route('enrollments.forceDelete', $enrollment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('تحذير: هل أنت تأكد من حذف التسجيل نهائياً؟ لا يمكن التراجع عن هذا الإجراء.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-custom" title="حذف نهائي">
                                                <i class="fa-solid fa-trash-can me-1"></i> حذف نهائي
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-muted">
                                    <i class="fa-solid fa-box-open fa-2x mb-3 d-block text-secondary"></i>
                                    لا توجد تسجيلات مؤرشفة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Notification Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                showToastNotification('عملية ناجحة', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showToastNotification('تنبيه', "{{ session('error') }}");
            @endif
        });

        function showToastNotification(title, message) {
            const oldToast = document.getElementById('realtime-toast');
            if (oldToast) oldToast.remove();

            const toast = document.createElement('div');
            toast.id = 'realtime-toast';
            toast.style.cssText = `
                position: fixed;
                bottom: 25px;
                left: 25px;
                background-color: #0f172a;
                color: #ffffff;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
                z-index: 999999;
                border-right: 5px solid #2563eb;
                direction: rtl;
                font-family: inherit;
                min-width: 290px;
                max-width: 400px;
                transition: all 0.4s ease;
            `;

            toast.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <span style="font-size: 20px; line-height: 1;">🔔</span>
                    <div style="flex-grow: 1;">
                        <strong style="font-size: 14px; display: block; color: #ffffff; margin-bottom: 2px;">${title}</strong>
                        <span style="font-size: 13px; color: #cbd5e1; display: block; line-height: 1.4;">${message}</span>
                    </div>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        }
    </script>
</body>
</html>