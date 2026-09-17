<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيلات الطلاب - EduPulse</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ⚡ استدعاء مكتبات Vite الخاصة بـ Reverb و Echo لاستقبال الإشعارات اللحظية -->
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
                        تسجيلات الشُعب الدراسية <i class="fa-solid fa-user-check text-secondary ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتتبع تسجيلات الطلاب في الشُعب والدورات</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('enrollments.create') }}" class="btn btn-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-plus me-1"></i> تسجيل طالب جديد
                    </a>

                    <a href="{{ route('enrollments.archive') }}" class="btn btn-warning text-dark btn-custom shadow-sm fw-bold">
                        <i class="fa-solid fa-box-archive me-1"></i> أرشيف التسجيلات
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

                                        <form action="{{ route('enrollments.destroy', $enrollment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من نقل التسجيل إلى الأرشيف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-custom" title="نقل إلى الأرشيف">
                                                <i class="fa-solid fa-box-archive"></i>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Real-Time & Flash Notifications Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // 1. إظهار التنبيه الخاطف (Toast) عند نجاح أو فشل العملية
            @if(session('success'))
                showToastNotification('عملية ناجحة', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showToastNotification('تنبيه', "{{ session('error') }}");
            @endif

            // 2. الاستماع اللحظي عبر Laravel Echo
            const userId = "{{ auth()->id() }}";

            if (userId && typeof window.Echo !== 'undefined') {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('🔔 تم استقبال إشعار لحظي:', notification);

                        const title = notification.title || (notification.data && notification.data.title) || 'إشعار جديد';
                        const message = notification.message || (notification.data && notification.data.message) || 'تمت العملية بنجاح';

                        showToastNotification(title, message);
                    });
            }
        });

        // دالة إنتاج نخب التنبيه (Toast Pop-up)
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