<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ⚡ تحديث عنوان الصفحة ليتناسق مع بقية النظام -->
    <title>إدارة الشُعب الدراسية - EduPulse</title>

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
        .badge-day { background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
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
                        إدارة الشُعب الدراسية <i class="fa-solid fa-chalkboard-user text-purple ms-2" style="color: #7c3aed;"></i>
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتعديل الشُعب والمواعيد والقاعات المتاحة</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('course-classes.create') }}" class="btn btn-primary btn-custom shadow-sm" style="background-color: #7c3aed; border: none;">
                        <i class="fa-solid fa-plus me-1"></i> إضافة شعبة جديدة
                    </a>

                    <!-- ⚡ تحديث نص زر الأرشيف ليصبح متناسقاً -->
                    <a href="{{ route('course-classes.archive') }}" class="btn btn-outline-warning btn-custom shadow-sm">
                        <i class="fa-solid fa-box-archive me-1"></i> أرشيف الشُعب
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
                            <th class="py-3">رقم الشعبة</th>
                            <th class="py-3 text-start">الدورة التدريبية</th>
                            <th class="py-3 text-start">المحاضر</th>
                            <th class="py-3">القاعة</th>
                            <th class="py-3">أيام المحاضرات</th>
                            <th class="py-3">التوقيت</th>
                            <th class="py-3">الحالة</th>
                            <th class="py-3">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                        <tr>
                            <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-light text-purple border fw-bold px-2 py-1 fs-6" style="color: #7c3aed; border-color: #7c3aed !important;">
                                    {{ $class->class_number }}
                                </span>
                            </td>
                            <td class="text-start fw-bold text-dark">{{ $class->course->title ?? 'غير محدد' }}</td>
                            <td class="text-start text-muted">{{ $class->teacher->name ?? 'غير محدد' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-door-open me-1 text-secondary"></i> {{ $class->classRoom->name ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                @if(is_array($class->days) && count($class->days) > 0)
                                    @foreach($class->days as $day)
                                        <span class="badge badge-day me-1">{{ $day }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted fw-bold">
                                    <i class="fa-regular fa-clock me-1 text-primary"></i>
                                    {{ $class->start_time ? \Carbon\Carbon::parse($class->start_time)->format('h:i A') : '--' }} 
                                    - 
                                    {{ $class->end_time ? \Carbon\Carbon::parse($class->end_time)->format('h:i A') : '--' }}
                                </small>
                            </td>
                            <td>
                                @if($class->status == 'upcoming')
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-hourglass-start me-1"></i> قادمة</span>
                                @elseif($class->status == 'ongoing')
                                    <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-play me-1"></i> جارية</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1"><i class="fa-solid fa-check me-1"></i> مكتملة</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('course-classes.edit', $class->id) }}" class="btn btn-sm btn-outline-warning btn-custom" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('course-classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger btn-custom" title="أرشفة" onclick="return confirm('هل أنت تأكد من نقل هذه الشعبة إلى الأرشيف؟')">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-5 text-muted">
                                <i class="fa-solid fa-chalkboard fa-2x mb-3 d-block text-secondary"></i>
                                لا توجد شُعب دراسية مضافة حتى الآن.
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

            // 1. إظهار التنبيه الخاطف (Toast) عند نجاح أو فشل العملية من الـ Session
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

        // دالة إنشاء وترسيم نافذة التنبيه الخاطفة (Toast Pop-up)
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
                border-right: 5px solid #7c3aed;
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