<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الدورات التدريبية - EduPulse</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ⚡ استدعاء مكتبات Vite الخاصة بـ Reverb و Echo لاستقبال الإشعارات -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
        .badge-custom { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }
        .course-img { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container">
        <!-- Header -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

                <!-- العنوان والوصف -->
                <div class="text-start">
                    <h3 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-graduation-cap text-primary me-2"></i> إدارة الدورات التدريبية
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتعديل كافة الدورات المسجلة في النظام</p>
                </div>

                <!-- الأزرار الأساسية -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('courses.create') }}" class="btn btn-primary btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-plus me-2"></i> إضافة دورة جديدة
                    </a>

                    <!-- 📦 زر الانتقال للأرشيف -->
                    <a href="{{ route('courses.archive') }}" class="btn btn-warning text-dark btn-custom shadow-sm d-inline-flex align-items-center fw-bold">
                        <i class="fa-solid fa-box-archive me-2"></i> أرشيف الدورات
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-gauge me-2"></i> لوحة التحكم
                    </a>
                </div>

            </div>
        </div>

        <!-- Table -->
        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3">المرفق/الصورة</th>
                            <th class="py-3 text-start">عنوان الدورة</th>
                            <th class="py-3">التصنيف</th>
                            <th class="py-3">المحاضر</th>
                            <th class="py-3">السعر</th>
                            <th class="py-3">عدد الساعات</th>
                            <th class="py-3">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                                
                                <!-- عمود الصورة/المرفق -->
                                <td>
                                    @if($course->avatar)
                                        @if(\Illuminate\Support\Str::endsWith($course->avatar, '.pdf'))
                                            <a href="{{ Storage::url($course->avatar) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="عرض ملف PDF">
                                                <i class="fa-solid fa-file-pdf fa-lg"></i>
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($course->avatar) }}" target="_blank">
                                                <img src="{{ Storage::url($course->avatar) }}" alt="{{ $course->title }}" class="course-img border shadow-sm">
                                            </a>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-image me-1"></i> لا يوجد
                                        </span>
                                    @endif
                                </td>

                                <td class="text-start fw-bold text-dark">
                                    <i class="fa-solid fa-book text-secondary me-2"></i> {{ $course->title }}
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 badge-custom">
                                        <i class="fa-solid fa-layer-group me-1"></i> {{ $course->category->name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td>
                                    @if($course->teacher)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-user-check me-1"></i> {{ $course->teacher->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 badge-custom">
                                            <i class="fa-solid fa-user-xmark me-1"></i> غير محدد
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">${{ number_format($course->price, 2) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border p-2">
                                        <i class="fa-regular fa-clock me-1"></i> {{ $course->total_hours }} ساعة
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-info btn-custom" title="عرض">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-outline-warning btn-custom" title="تعديل">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-custom" title="نقل إلى الأرشيف" onclick="return confirm('هل أنت متأكد من نقل هذه الدورة إلى الأرشيف؟')">
                                                <i class="fa-solid fa-box-archive"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">
                                    <i class="fa-solid fa-book-open fa-2x mb-3 d-block text-secondary"></i>
                                    لا توجد دورات تدريبية مضافة حتى الآن.
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

            @if(session('success'))
                showToastNotification('عملية ناجحة', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showToastNotification('تنبيه', "{{ session('error') }}");
            @endif

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