<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف المحاضرين - EduPulse</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ⚡ استدعاء مكتبات Vite الخاصة بـ Reverb و Echo -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
        .badge-spec { background-color: #fee2e2; color: #991b1b; font-weight: 600; padding: 6px 12px; border-radius: 20px; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container">
        <!-- Header Section -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-box-archive text-warning me-2"></i> أرشيف المحاضرين المحذوفين
                    </h3>
                    <p class="text-muted mb-0 small">استعادة أو حذف المحاضرين بشكل نهائي من قاعدة البيانات</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <!-- زر حذف كل المحذوفات (تفريغ الأرشيف) -->
                    @if($trashedTeachers->count() > 0)
                        <form action="{{ route('teachers.archive.force-delete-all') }}" method="POST" class="d-inline" onsubmit="return confirm('تنبيه هام: هل أنت متأكد من رغبتك في حذف جميع المحاضرين الموجودين في الأرشيف نهائياً؟ لا يمكن استعادة البيانات بعد هذه الخطوة!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-custom shadow-sm d-inline-flex align-items-center">
                                <i class="fa-solid fa-trash-can me-2"></i> حذف كل المحذوفات
                            </button>
                        </form>
                    @endif

                    <!-- زر العودة للقائمة -->
                    <a href="{{ route('teachers.index') }}" class="btn btn-primary btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-arrow-right me-2"></i> العودة لقائمة المحاضرين
                    </a>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3 text-start">اسم المحاضر</th>
                            <th class="py-3">البريد الإلكتروني</th>
                            <th class="py-3">التخصص</th>
                            <th class="py-3">تاريخ الحذف</th>
                            <th class="py-3">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedTeachers as $teacher)
                        <tr>
                            <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                            <td class="text-start fw-bold text-dark">
                                <i class="fa-solid fa-user-slash text-secondary me-2"></i>{{ $teacher->name }}
                            </td>
                            <td class="text-muted">{{ $teacher->email }}</td>
                            <td><span class="badge-spec">{{ $teacher->specialization }}</span></td>
                            <td class="text-muted small fw-semibold">
                                {{ $teacher->deleted_at ? $teacher->deleted_at->diffForHumans() : '' }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- زر الاستعادة -->
                                    <form action="{{ route('teachers.restore', $teacher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success btn-custom d-inline-flex align-items-center gap-1" title="استعادة">
                                            <i class="fa-solid fa-rotate-left"></i> استعادة
                                        </button>
                                    </form>

                                    <!-- زر الحذف النهائي -->
                                    <form action="{{ route('teachers.forceDelete', $teacher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger btn-custom d-inline-flex align-items-center gap-1" title="حذف نهائي" onclick="return confirm('تحذير: سيتم حذف المحاضر نهائياً ولن تتمكن من استرجاعه!')">
                                            <i class="fa-solid fa-trash-can"></i> حذف نهائي
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-muted">
                                <i class="fa-solid fa-folder-open fa-2x mb-3 d-block text-secondary"></i>
                                لا يوجد محاضرون في الأرشيف حالياً.
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

            // إظهار الرسالة السريعة (Toast) عند النجاح أو الفشل
            @if(session('success'))
                showToastNotification('عملية ناجحة', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showToastNotification('تنبيه', "{{ session('error') }}");
            @endif

            // الاستماع للإشعارات اللحظية
            const userId = "{{ auth()->id() }}";

            if (userId && typeof window.Echo !== 'undefined') {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        const title = notification.title || (notification.data && notification.data.title) || 'إشعار جديد';
                        const message = notification.message || (notification.data && notification.data.message) || 'تم تحديث البيانات بنجاح';

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