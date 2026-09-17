<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EduPulse - لوحة التحكم')</title>
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- تحميل ملفات Vite (Laravel Echo / Reverb) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        .card-custom { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            transition: transform 0.2s; 
        }
        .card-custom:hover { 
            transform: translateY(-3px); 
        }
        .stat-icon { 
            width: 50px; 
            height: 50px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
        }
    </style>
    @stack('styles')
</head>
<body class="p-3 p-md-4">

    <!-- Navbar العلوي -->
    <nav class="navbar navbar-expand-lg bg-white card-custom p-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            
            <!-- Logo & Brand -->
            <a class="navbar-brand fw-bold text-primary fs-4 d-flex align-items-center gap-2 m-0" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-graduation-cap fs-3"></i> EduPulse
            </a>

            <!-- Notifications, User Info & Logout Button -->
            <div class="d-flex align-items-center gap-3">
                
                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative border px-3 py-2 rounded-3" type="button" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bell text-secondary fs-5"></i>
                        @php
                            $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
                        @endphp
                        <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="{{ $unreadCount > 0 ? '' : 'display: none;' }}">
                            {{ $unreadCount }}
                        </span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg border-0" aria-labelledby="notificationBell" style="width: 330px; max-height: 400px; overflow-y: auto;">
                        <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom px-2">
                            <span class="fw-bold text-dark"><i class="fa-solid fa-bell text-primary me-1"></i> الإشعارات</span>
                            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted p-0 style-none" style="font-size: 0.8rem;">تحديد الكل كمقروء</button>
                                </form>
                            @endif
                        </li>

                        @if(auth()->check() && auth()->user()->notifications->count() > 0)
                            @foreach(auth()->user()->notifications->take(5) as $notification)
                                <li class="p-2 mb-1 rounded {{ $notification->read_at ? 'bg-light' : 'bg-white border-start border-primary border-3' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="d-block text-dark small">{{ $notification->data['title'] ?? 'إشعار جديد' }}</strong>
                                            <span class="text-muted d-block" style="font-size: 0.85rem;">{{ $notification->data['message'] ?? '' }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(is_null($notification->read_at))
                                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm text-success p-0 ms-2" title="تحديد كمقروء">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="text-center text-muted p-3 small">لا يوجد إشعارات حالياً</li>
                        @endif
                    </ul>
                </div>

                <!-- User Info -->
                <span class="fw-bold text-dark d-flex align-items-center gap-2 bg-light px-3 py-2 rounded-3 border">
                    <i class="fa-solid fa-user-circle text-primary fs-5"></i> 
                    {{ auth()->user()->name ?? 'yousef_alqassas' }}
                </span>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger fw-bold d-flex align-items-center gap-1">
                        <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
                    </button>
                </form>
            </div>

        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Real-Time & Flash Notifications Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // 1. إظهار التنبيه من الـ Session بعد التحويل
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

                        // زيادة عداد الجرس
                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            let currentCount = parseInt(badge.innerText) || 0;
                            badge.innerText = currentCount + 1;
                            badge.style.display = 'inline-block';
                        }

                        // قراءة البيانات سواء جاءت مباشرة أو داخل notification.title / data
                        const title = notification.title || (notification.data && notification.data.title) || 'إشعار جديد';
                        const message = notification.message || (notification.data && notification.data.message) || 'تمت العملية بنجاح';

                        showToastNotification(title, message);
                    });
            }
        });

        // دالة إنشاء التنبيه الخاطف (Toast Pop-up)
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

    @stack('scripts')
</body>
</html>