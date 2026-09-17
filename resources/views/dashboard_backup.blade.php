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
        }

        /* شريط التصفح العلوي ثوابت وتنسيق مخصص بدون hover يخرب الصفحة */
        .navbar-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            background-color: #ffffff;
        }

        /* تحسين حركة الـ Hover للبطاقات فقط لتصبح سلسة وبدون اهتزاز أو تحريك للمحتوى */
        .card-custom { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            transition: transform 0.3s ease, box-shadow 0.3s ease !important; 
        }
        .card-custom:hover { 
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }

        .stat-icon { 
            width: 48px; 
            height: 48px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.4rem; 
        }
        
        /* عزل قائمة الإشعارات تماماً لكي تطفو فوق الصفحة دون دفع المحتوى لأسفل */
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

        /* تنسيق وتفعيل Hover زر إدارة الشعب البنفسجي */
        .btn-outline-purple {
            color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-outline-purple:hover {
            background-color: #6f42c1 !important;
            color: #ffffff !important;
            border-color: #6f42c1 !important;
        }
    </style>
</head>
<body class="p-3 p-md-4">

    <!-- Header / Navbar العلوي -->
    <nav class="navbar navbar-expand-lg navbar-custom p-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            
            <!-- Logo & Brand -->
            <a class="navbar-brand fw-bold text-primary fs-4 d-flex align-items-center gap-2 m-0" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-graduation-cap fs-3"></i> EduPulse
            </a>

            <!-- Notifications, User Info & Logout -->
            <div class="d-flex align-items-center gap-3">

                <!-- 🔔 جرس التنبيهات -->
                <div class="dropdown notification-dropdown-container">
                    <button class="btn btn-light position-relative border px-3 py-2 rounded-3" type="button" id="notificationBell" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
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

    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="card card-custom p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0">
                        لوحة التحكم الرئيسية <i class="fa-solid fa-gauge-high text-primary ms-2"></i>
                    </h2>
                    <p class="text-muted mb-0 mt-1">مرحباً بك في نظام إدارة المنصة التعليمية EduPulse</p>
                </div>
                <div>
                    <span class="badge bg-light text-dark p-3 border fw-semibold">
                        <i class="fa-regular fa-calendar-check me-1 text-primary"></i> {{ date('Y-m-d') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <!-- 1. الطلاب -->
            <div class="col-12 col-md-6 col-lg-4">
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
            <div class="col-12 col-md-6 col-lg-4">
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
            <div class="col-12 col-md-6 col-lg-4">
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
            <div class="col-12 col-md-6 col-lg-4">
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

            <!-- 5. القاعات الدراسية -->
            <div class="col-12 col-md-6 col-lg-4">
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

            <!-- 6. الشُعب الدراسية -->
            <div class="col-12 col-md-6 col-lg-4">
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

            <!-- 7. تسجيلات الطلاب -->
            <div class="col-12 col-md-6 col-lg-4">
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

            <!-- 8. سجلات الحضور -->
            <div class="col-12 col-md-6 col-lg-4">
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

            <!-- 9. المدفوعات والأقساط -->
            <div class="col-12 col-md-6 col-lg-4">
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

        <!-- Quick Access Navigation -->
        <h5 class="fw-bold text-dark mb-3">الوصول السريع للأقسام</h5>
        <div class="row g-3 mb-4">
            <!-- 1. الطلاب -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-users text-success fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">إدارة الطلاب</h6>
                    <p class="text-muted small mb-3">عرض وتعديل كافة الطلاب</p>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-success fw-bold w-100 mt-auto">الانتقال للطلاب</a>
                </div>
            </div>

            <!-- 2. الدورات -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-book-open text-primary fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">إدارة الدورات</h6>
                    <p class="text-muted small mb-3">إدارة الدورات والأسعار</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-primary fw-bold w-100 mt-auto">الانتقال للدورات</a>
                </div>
            </div>

            <!-- 3. المحاضرين -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-chalkboard-user text-info fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">إدارة المحاضرين</h6>
                    <p class="text-muted small mb-3">إدارة بيانات المحاضرين</p>
                    <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-outline-info fw-bold w-100 mt-auto">الانتقال للمحاضرين</a>
                </div>
            </div>

            <!-- 4. التصنيفات -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-tags text-warning fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">إدارة التصنيفات</h6>
                    <p class="text-muted small mb-3">تصنيف الدورات حسب المجال</p>
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-warning fw-bold w-100 mt-auto">الانتقال للتصنيفات</a>
                </div>
            </div>

            <!-- 5. القاعات -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-door-open fs-2 mb-2" style="color: #6610f2;"></i>
                    <h6 class="fw-bold mb-1">القاعات الدراسية</h6>
                    <p class="text-muted small mb-3">إدارة القاعات وسعاتها</p>
                    <a href="{{ route('class-rooms.index') }}" class="btn btn-sm btn-outline-primary fw-bold w-100 mt-auto" style="color: #6610f2; border-color: #6610f2;">الانتقال للقاعات</a>
                </div>
            </div>

            <!-- 6. الشُعب (تم إصلاح وإضافة الـ Hover هنا) -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-chalkboard fs-2 mb-2" style="color: #6f42c1;"></i>
                    <h6 class="fw-bold mb-1">إدارة الشُعب</h6>
                    <p class="text-muted small mb-3">تنظيم المواعيد والشُعب</p>
                    <a href="{{ route('course-classes.index') }}" class="btn btn-sm btn-outline-purple fw-bold w-100 mt-auto">الانتقال للشُعب</a>
                </div>
            </div>

            <!-- 7. تسجيلات الطلاب -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-user-check text-secondary fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">تسجيلات الطلاب</h6>
                    <p class="text-muted small mb-3">متابعة تسجيلات الطلاب</p>
                    <a href="{{ route('enrollments.index') }}" class="btn btn-sm btn-outline-secondary fw-bold w-100 mt-auto">الانتقال للتسجيلات</a>
                </div>
            </div>

            <!-- 8. الحضور -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-clipboard-user text-danger fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">الحضور والغياب</h6>
                    <p class="text-muted small mb-3">تسجيل ومتابعة الحضور</p>
                    <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-outline-danger fw-bold w-100 mt-auto">الانتقال للحضور</a>
                </div>
            </div>

            <!-- 9. المدفوعات -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card card-custom p-3 bg-white text-center h-100">
                    <i class="fa-solid fa-receipt text-success fs-2 mb-2"></i>
                    <h6 class="fw-bold mb-1">المدفوعات والأقساط</h6>
                    <p class="text-muted small mb-3">متابعة الدفعات وإصدار السندات</p>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-success fw-bold w-100 mt-auto">الانتقال للمدفوعات</a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card card-custom p-4 bg-white">
            <h5 class="fw-bold text-dark mb-3">عمليات إضافة سريعة</h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('students.create') }}" class="btn btn-sm btn-success fw-bold"><i class="fa-solid fa-plus me-1"></i> إضافة طالب</a>
                <a href="{{ route('courses.create') }}" class="btn btn-sm btn-primary fw-bold"><i class="fa-solid fa-plus me-1"></i> إضافة دورة</a>
                <a href="{{ route('teachers.create') }}" class="btn btn-sm btn-info text-white fw-bold"><i class="fa-solid fa-plus me-1"></i> إضافة محاضر</a>
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-warning text-white fw-bold"><i class="fa-solid fa-plus me-1"></i> إضافة تصنيف</a>
                <a href="{{ route('class-rooms.create') }}" class="btn btn-sm text-white fw-bold" style="background-color: #6610f2;"><i class="fa-solid fa-plus me-1"></i> إضافة قاعة جديدة</a>
                <a href="{{ route('course-classes.create') }}" class="btn btn-sm fw-bold text-white" style="background-color: #6f42c1;"><i class="fa-solid fa-plus me-1"></i> إضافة شعبة دراسية</a>
                <a href="{{ route('attendances.create') }}" class="btn btn-sm btn-danger fw-bold"><i class="fa-solid fa-plus me-1"></i> تسجيل حضور جديد</a>
                <a href="{{ route('enrollments.create') }}" class="btn btn-sm btn-secondary fw-bold"><i class="fa-solid fa-plus me-1"></i> تسجيل طالب جديد</a>
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-plus me-1"></i> تسجيل دفعة جديدة</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>