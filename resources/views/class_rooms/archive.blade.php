<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف القاعات الدراسية</title>

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
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container">
        <!-- Header Section -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

                <!-- Title (اليمين) -->
                <div class="text-center text-md-start">
                    <h3 class="fw-bold mb-1 text-dark">
                        أرشيف القاعات الدراسية <i class="fa-solid fa-box-archive text-warning ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">استعادة أو حذف القاعات المؤرشفة نهائياً من النظام</p>
                </div>

                <!-- Action Buttons (اليسار) -->
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    @if($classRooms->count() > 0)
                        <!-- زر تفريغ الأرشيف بالكامل -->
                        <form action="{{ route('class-rooms.forceDeleteAllArchive') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-custom shadow-sm" onclick="return confirm('تحذير شديد: هل أنت متأكد من تفريغ الأرشيف بالكامل؟ سيتم حذف جميع القاعات المؤرشفة نهائياً ولن يمكنك استعادتها!')">
                                <i class="fa-solid fa-dumpster-fire me-1"></i> تفريغ الأرشيف
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('class-rooms.index') }}" class="btn btn-primary btn-custom shadow-sm">
                        <i class="fa-solid fa-arrow-right me-1"></i> العودة للقاعات النشطة
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
                            <th class="py-3">اسم القاعة</th>
                            <th class="py-3">السعة الاستيعابية</th>
                            <th class="py-3">تاريخ الأرشفة</th>
                            <th class="py-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classRooms as $classRoom)
                        <tr>
                            <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                            <td class="fw-bold text-dark">{{ $classRoom->name }}</td>
                            <td>
                                <span class="badge bg-info text-dark p-2 fs-6">
                                    {{ $classRoom->capacity }} طالب
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $classRoom->deleted_at->format('Y-m-d (h:i A)') }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- زر الاستعادة من الأرشيف -->
                                    <form action="{{ route('class-rooms.restore', $classRoom->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success btn-custom" title="استعادة القاعة" onclick="return confirm('هل ترغب في استعادة هذه القاعة إلى القائمة النشطة؟')">
                                            <i class="fa-solid fa-rotate-left me-1"></i> استعادة
                                        </button>
                                    </form>

                                    <!-- زر الحذف النهائي من قاعدة البيانات -->
                                    <form action="{{ route('class-rooms.forceDelete', $classRoom->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger btn-custom" title="حذف نهائي" onclick="return confirm('تحذير: هل أنت متأكد من حذف هذه القاعة نهائياً؟ لا يمكن التراجع عن هذا الإجراء!')">
                                            <i class="fa-solid fa-trash me-1"></i> حذف نهائي
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-3 d-block text-secondary"></i>
                                الأرشيف فارغ حالياً، لا توجد قاعات مؤرشفة.
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

    <!-- Flash Notifications Script -->
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