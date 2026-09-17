<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف السندات والمدفوعات - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid">
        <!-- Header -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        أرشيف المدفوعات والسندات <i class="fa-solid fa-box-archive text-warning ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">استعادة السندات المحذوفة أو حذفها نهائياً من النظام</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($payments->count() > 0)
                        <form action="{{ route('payments.archive.force-delete-all') }}" method="POST" class="d-inline" onsubmit="return confirm('تنبيه هام جداً: هل أنت متأكد من رغبتك في حذف جميع سندات القبض الموجودة في الأرشيف نهائياً؟ لا يمكن التراجع عن هذا الإجراء!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-custom shadow-sm d-inline-flex align-items-center">
                                <i class="fa-solid fa-trash-can me-2"></i> تفريغ الأرشيف
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-arrow-right me-1"></i> العودة لقائمة المدفوعات
                    </a>
                </div>
            </div>
        </div>

        <!-- Notification Alert -->
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

        <!-- Filter & Table Card -->
        <div class="card card-custom overflow-hidden mb-4">
            <div class="p-4 border-bottom bg-white">
                <form method="GET" action="{{ route('payments.archive') }}" class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="بحث باسم الطالب أو رقم السند في الأرشيف..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-custom text-dark w-100">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> بحث
                        </button>
                        <a href="{{ route('payments.archive') }}" class="btn btn-outline-secondary btn-custom">إلغاء</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="py-3">رقم السند</th>
                            <th class="py-3 text-start">اسم الطالب</th>
                            <th class="py-3">المبلغ المقبوض</th>
                            <th class="py-3">تاريخ الأرشفة</th>
                            <th class="py-3">إجراءات الاستعادة والحذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="fw-bold text-secondary">#{{ $payment->receipt_number ?? $payment->id }}</td>
                                <td class="text-start fw-bold text-dark">{{ $payment->student->name ?? 'غير محدد' }}</td>
                                <td class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                <td class="text-muted small fw-semibold">{{ \Carbon\Carbon::parse($payment->deleted_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <!-- استعادة -->
                                    <form action="{{ route('payments.restore', $payment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success me-1" title="استعادة السند">
                                            <i class="fa-solid fa-rotate-left me-1"></i> استعادة
                                        </button>
                                    </form>

                                    <!-- حذف نهائي -->
                                    <form action="{{ route('payments.forceDelete', $payment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('تنبيه هام: سيتم حذف السند نهائياً ولا يمكن استرجاعه مجدداً! هل تريد المتابعة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="حذف نهائي">
                                            <i class="fa-solid fa-trash-can me-1"></i> حذف نهائي
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-muted">
                                    <i class="fa-solid fa-box-open fa-2x mb-3 d-block text-secondary"></i>
                                    لا يوجد مدفوعات مؤرشفة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($payments, 'links'))
                <div class="p-3 bg-light border-top">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toast Notification Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            @if(session('success'))
                showToastNotification('تمت العملية بنجاح', "{{ session('success') }}");
            @endif

            const userId = "{{ auth()->id() }}";

            if (userId && typeof window.Echo !== 'undefined') {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        const title = notification.title || (notification.data && notification.data.title) || 'تحديث مالي';
                        const message = notification.message || (notification.data && notification.data.message) || 'تم تحديث سجل المدفوعات';

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
                border-right: 5px solid #16a34a;
                direction: rtl;
                font-family: inherit;
                min-width: 290px;
                max-width: 400px;
                transition: all 0.4s ease;
            `;

            toast.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <span style="font-size: 20px; line-height: 1;">🗑️</span>
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