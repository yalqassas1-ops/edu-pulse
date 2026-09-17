<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - EduPulse</title>
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-left: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            z-index: 1000;
            box-shadow: -2px 0 15px rgba(0,0,0,0.03);
        }

        .main-content {
            margin-right: 260px;
            flex-grow: 1;
            padding: 1.5rem;
            width: calc(100% - 260px);
        }

        .sidebar .nav-link {
            color: #495057;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #f0f4ff;
            color: #0d6efd;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .navbar-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            background-color: #ffffff;
        }

        .card-custom { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            transition: transform 0.2s ease, box-shadow 0.2s ease !important; 
        }
        .card-custom:hover { 
            transform: translateY(-3px) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
        }

        .stat-icon { 
            width: 44px; 
            height: 44px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.3rem; 
        }

        /* 🎨 كروت الإضافة السريعة الحديثة (9 كروت) */
        .action-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #334155;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            color: #0d6efd;
            border-color: #cbd5e1;
        }

        .action-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .notification-dropdown-container {
            position: relative !important;
        }
        .dropdown-menu-notifications {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            z-index: 1055 !important;
            margin-top: 0.5rem !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-right: 0;
                width: 100%;
            }
            .wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- 📌 القائمة الجانبية (Sidebar) -->
    <aside class="sidebar p-3">
        <a class="navbar-brand fw-bold text-primary fs-4 d-flex align-items-center gap-2 mb-4 px-2" href="{{ route('dashboard') }}">
            <i class="fa-solid fa-graduation-cap fs-3"></i> EduPulse
        </a>

        <div class="text-muted small fw-bold px-2 mb-2">الوصول السريع للأقسام</div>

        <nav class="nav nav-pills flex-column mb-auto">
            <a class="nav-link active" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-gauge-high"></i> الرئيسية
            </a>
            <a class="nav-link" href="{{ route('students.index') }}">
                <i class="fa-solid fa-users text-success"></i> الطلاب
            </a>
            <a class="nav-link" href="{{ route('courses.index') }}">
                <i class="fa-solid fa-book-open text-primary"></i> الدورات
            </a>
            <a class="nav-link" href="{{ route('teachers.index') }}">
                <i class="fa-solid fa-chalkboard-user text-info"></i> المحاضرين
            </a>
            <a class="nav-link" href="{{ route('categories.index') }}">
                <i class="fa-solid fa-tags text-warning"></i> التصنيفات
            </a>
            <a class="nav-link" href="{{ route('class-rooms.index') }}">
                <i class="fa-solid fa-door-open" style="color: #6610f2;"></i> القاعات
            </a>
            <a class="nav-link" href="{{ route('course-classes.index') }}">
                <i class="fa-solid fa-chalkboard" style="color: #6f42c1;"></i> الشُعب
            </a>
            <a class="nav-link" href="{{ route('enrollments.index') }}">
                <i class="fa-solid fa-user-check text-secondary"></i> التسجيلات
            </a>
            <a class="nav-link" href="{{ route('attendances.index') }}">
                <i class="fa-solid fa-clipboard-user text-danger"></i> الحضور والغياب
            </a>
            <a class="nav-link" href="{{ route('payments.index') }}">
                <i class="fa-solid fa-receipt text-success"></i> المدفوعات
            </a>
        </nav>

        <div class="pt-3 border-top mt-2">
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger fw-bold w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- 📌 المحتوى الرئيسي -->
    <main class="main-content">

        <!-- Header العلوي -->
        <nav class="navbar navbar-custom p-3 mb-4">
            <div class="container-fluid d-flex justify-content-between align-items-center p-0">
                
                <div>
                    <h4 class="fw-bold text-dark m-0">لوحة التحكم الرئيسية</h4>
                    <small class="text-muted">مرحباً بك في نظام إدارة المنصة التعليمية EduPulse</small>
                </div>

                <div class="d-flex align-items-center gap-3">

                    <!-- 🔔 الإشعارات -->
                    <div class="dropdown notification-dropdown-container">
                        <button class="btn btn-light position-relative border px-3 py-2 rounded-3" type="button" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bell text-secondary fs-5"></i>
                            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-notifications p-2 shadow-lg border-0" aria-labelledby="notificationBell">
                            <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom px-2">
                                <span class="fw-bold text-dark"><i class="fa-solid fa-bell text-primary me-1"></i> الإشعارات</span>
                                @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted p-0" style="font-size: 0.8rem;">تحديد الكل كمقروء</button>
                                    </form>
                                @endif
                            </li>

                            @if(auth()->check() && auth()->user()->notifications->count() > 0)
                                @foreach(auth()->user()->notifications->take(5) as $notification)
                                    <li class="p-2 mb-1 rounded {{ $notification->read_at ? 'bg-light' : 'bg-white border-start border-primary border-3' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="text-end w-100">
                                                <strong class="d-block text-dark small fw-bold mb-1">{{ $notification->data['title'] ?? 'إشعار جديد' }}</strong>
                                                <span class="text-muted d-block text-wrap mb-1" style="font-size: 0.85rem;">{{ $notification->data['message'] ?? '' }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if(is_null($notification->read_at))
                                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="m-0 ms-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm text-success p-0" title="تحديد كمقروء">
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

                    <span class="badge bg-light text-dark p-2 border fw-semibold d-none d-md-inline-block">
                        <i class="fa-regular fa-calendar-check me-1 text-primary"></i> {{ date('Y-m-d') }}
                    </span>

                </div>

            </div>
        </nav>

        <!-- Stat Cards الإحصائيات الـ 9 -->
        <div class="row g-3 mb-4">
            <!-- 1. الطلاب -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">إجمالي الطلاب</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\Student::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. الدورات -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">الدورات التدريبية</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\Course::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. المحاضرين -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">المحاضرين</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\Teacher::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. التصنيفات -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">التصنيفات</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\Category::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. القاعات -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">القاعات الدراسية</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\ClassRoom::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-opacity-10" style="background-color: rgba(102, 16, 242, 0.1); color: #6610f2;">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. الشُعب -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">الشُعب الدراسية</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\CourseClass::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-opacity-10" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="fa-solid fa-chalkboard"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. التسجيلات -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">تسجيلات الطلاب</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\Enrollment::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. الحضور -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">سجلات الحضور</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\Attendance::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 9. التحصيلات -->
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">إجمالي التحصيلات</span>
                            <h3 class="fw-bold m-0 text-success">${{ number_format(\App\Models\Payment::sum('amount') ?? 0, 2) }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ⚡ كروت عمليات الإضافة السريعة الحديثة (الـ 9 كاملاً) -->
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-bolt text-warning"></i> عمليات إضافة سريعة
            </h5>
            
            <div class="row g-3">
                <!-- 1. إضافة طالب -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('students.create') }}" class="action-card">
                        <div class="action-card-icon bg-success"><i class="fa-solid fa-user-plus"></i></div>
                        <span>إضافة طالب</span>
                    </a>
                </div>

                <!-- 2. إضافة دورة -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('courses.create') }}" class="action-card">
                        <div class="action-card-icon bg-primary"><i class="fa-solid fa-book"></i></div>
                        <span>إضافة دورة</span>
                    </a>
                </div>

                <!-- 3. إضافة محاضر -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('teachers.create') }}" class="action-card">
                        <div class="action-card-icon bg-info"><i class="fa-solid fa-chalkboard-user"></i></div>
                        <span>إضافة محاضر</span>
                    </a>
                </div>

                <!-- 4. إضافة تصنيف -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('categories.create') }}" class="action-card">
                        <div class="action-card-icon bg-warning"><i class="fa-solid fa-tags"></i></div>
                        <span>إضافة تصنيف</span>
                    </a>
                </div>

                <!-- 5. إضافة قاعة -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('class-rooms.create') }}" class="action-card">
                        <div class="action-card-icon" style="background-color: #6610f2;"><i class="fa-solid fa-door-open"></i></div>
                        <span>إضافة قاعة</span>
                    </a>
                </div>

                <!-- 6. إضافة شعبة -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('course-classes.create') }}" class="action-card">
                        <div class="action-card-icon" style="background-color: #6f42c1;"><i class="fa-solid fa-chalkboard"></i></div>
                        <span>إضافة شعبة</span>
                    </a>
                </div>

                <!-- 7. تسجيل حضور -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('attendances.create') }}" class="action-card">
                        <div class="action-card-icon bg-danger"><i class="fa-solid fa-clipboard-user"></i></div>
                        <span>تسجيل حضور</span>
                    </a>
                </div>

                <!-- 8. تسجيل طالب جديد -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('enrollments.create') }}" class="action-card">
                        <div class="action-card-icon bg-secondary"><i class="fa-solid fa-user-check"></i></div>
                        <span>تسجيل طالب جديد</span>
                    </a>
                </div>

                <!-- 9. تسجيل دفعة -->
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('payments.index') }}" class="action-card">
                        <div class="action-card-icon bg-success"><i class="fa-solid fa-receipt"></i></div>
                        <span>تسجيل دفعة</span>
                    </a>
                </div>
            </div>
        </div>

    </main>

</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>