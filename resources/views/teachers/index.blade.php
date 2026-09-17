<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المحاضرين - EduPulse</title>

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
        .badge-spec { background-color: #e0e7ff; color: #3730a3; font-weight: 600; padding: 6px 12px; border-radius: 20px; }
        .badge-custom { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container">
        <!-- Header Section -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-chalkboard-user text-primary me-2"></i> إدارة المحاضرين
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتعديل بيانات المحاضرين المسجلين في المركز</p>
                </div>
                <!-- الأزرار الأساسية -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-user-plus me-2"></i> إضافة محاضر جديد
                    </a>
                    <!-- زر الانتقال للأرشيف -->
                    <a href="{{ route('teachers.archive') }}" class="btn btn-warning text-dark btn-custom shadow-sm d-inline-flex align-items-center fw-bold">
                        <i class="fa-solid fa-box-archive me-2"></i> أرشيف المحاضرين
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-gauge me-2"></i> لوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- 📊 Excel Actions Section (مطابق لصفحة الطلاب) -->
        <div class="card card-custom p-3 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <!-- زر تصدير Excel (يمين) -->
                <div>
                    <a href="{{ route('teachers.export') }}" class="btn btn-success btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-file-excel me-2"></i> تصدير البيانات إلى Excel
                    </a>
                </div>

                <!-- نموذج استيراد Excel (يسار) -->
                <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2 m-0">
                    @csrf
                    <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    <button type="submit" class="btn btn-primary btn-custom shadow-sm d-inline-flex align-items-center text-nowrap">
                        <i class="fa-solid fa-file-import me-2"></i> استيراد Excel
                    </button>
                </form>
            </div>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
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
                            <th class="py-3 text-start">اسم المحاضر</th>
                            <th class="py-3">البريد الإلكتروني</th>
                            <th class="py-3">رقم الهاتف</th>
                            <th class="py-3">التخصص</th>
                            <th class="py-3">المرفق / الصورة</th>
                            <th class="py-3">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr>
                            <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                            <td class="text-start fw-bold text-dark">
                                <i class="fa-solid fa-user-tie text-secondary me-2"></i>{{ $teacher->name }}
                            </td>
                            <td class="text-muted">{{ $teacher->email }}</td>
                            <td class="text-muted dir-ltr">{{ $teacher->phone ?? 'غير مدخل' }}</td>
                            <td><span class="badge-spec">{{ $teacher->specialization ?? 'عام' }}</span></td>
                            
                            <!-- 📁 عرض المرفق / الصورة ديناميكياً -->
                            <td>
                                @if($teacher->attachment)
                                    <a href="{{ Storage::url($teacher->attachment) }}" target="_blank" class="btn btn-sm btn-outline-info btn-custom">
                                        <i class="fa-solid fa-paperclip me-1"></i> عرض المرفق
                                    </a>
                                @else
                                    <span class="text-muted small">لا يوجد مرفق</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-warning btn-custom" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <!-- زر الحذف المؤقت (Soft Delete) -->
                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger btn-custom" title="نقل إلى الأرشيف" onclick="return confirm('هل أنت تأكد من نقل هذا المحاضر إلى الأرشيف؟')">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-muted">
                                <i class="fa-solid fa-user-slash fa-2x mb-3 d-block text-secondary"></i>
                                لا يوجد محاضرون مضافون حتى الآن.
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