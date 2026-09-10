<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .card-custom:hover { transform: translateY(-3px); }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container-fluid">
        <!-- Header -->
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
        <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-9">
            <!-- الطلاب -->
            <div class="col">
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

            <!-- الدورات -->
            <div class="col">
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

            <!-- المحاضرين -->
            <div class="col">
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

            <!-- التصنيفات -->
            <div class="col">
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

            <!-- القاعات الدراسية -->
            <div class="col">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">القاعات الدراسية</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\ClassRoom::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-indigo bg-opacity-10 text-indigo" style="background-color: rgba(102, 16, 242, 0.1); color: #6610f2;">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشُعب الدراسية -->
            <div class="col">
                <div class="card card-custom p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-bold">الشُعب الدراسية</span>
                            <h3 class="fw-bold m-0 text-dark">{{ \App\Models\CourseClass::count() ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-purple bg-opacity-10 text-purple" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="fa-solid fa-chalkboard"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تسجيلات الطلاب -->
            <div class="col">
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

            <!-- سجلات الحضور -->
            <div class="col">
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

            <!-- المدفوعات والأقساط (مضاف حديثاً) -->
            <div class="col">
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
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-users text-success fs-1 mb-3"></i>
                    <h5 class="fw-bold">إدارة الطلاب</h5>
                    <p class="text-muted small">عرض، إضافة، وتعديل بيانات كافة الطلاب</p>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-success fw-bold w-100 mt-auto">الانتقال للطلاب</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-book-open text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">إدارة الدورات</h5>
                    <p class="text-muted small">إدارة الدورات وساعاتها وأسعارها والمحاضرين</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary fw-bold w-100 mt-auto">الانتقال للدورات</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-chalkboard-user text-info fs-1 mb-3"></i>
                    <h5 class="fw-bold">إدارة المحاضرين</h5>
                    <p class="text-muted small">إدارة بيانات المحاضرين والمدربين في المنصة</p>
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-info fw-bold w-100 mt-auto">الانتقال للمحاضرين</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-tags text-warning fs-1 mb-3"></i>
                    <h5 class="fw-bold">إدارة التصنيفات</h5>
                    <p class="text-muted small">تنظيم وتصنيف الدورات حسب مجالاتها</p>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-warning fw-bold w-100 mt-auto">الانتقال للتصنيفات</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-door-open fs-1 mb-3" style="color: #6610f2;"></i>
                    <h5 class="fw-bold">القاعات الدراسية</h5>
                    <p class="text-muted small">إدارة القاعات وسعاتها الاستيعابية</p>
                    <a href="{{ route('class-rooms.index') }}" class="btn btn-outline-primary fw-bold w-100 mt-auto" style="color: #6610f2; border-color: #6610f2;">الانتقال للقاعات</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-chalkboard fs-1 mb-3" style="color: #6f42c1;"></i>
                    <h5 class="fw-bold">إدارة الشُعب</h5>
                    <p class="text-muted small">تنظيم مواعيد وتوقيتات الشُعب والقاعات</p>
                    <a href="{{ route('course-classes.index') }}" class="btn btn-outline-purple fw-bold w-100 mt-auto" style="color: #6f42c1; border-color: #6f42c1;">الانتقال للشُعب</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-user-check text-secondary fs-1 mb-3"></i>
                    <h5 class="fw-bold">تسجيلات الطلاب</h5>
                    <p class="text-muted small">عرض وتتبع تسجيلات الطلاب في الدورات</p>
                    <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary fw-bold w-100 mt-auto">الانتقال للتسجيلات</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-clipboard-user text-danger fs-1 mb-3"></i>
                    <h5 class="fw-bold">الحضور والغياب</h5>
                    <p class="text-muted small">تسجيل ومتابعة حضور وغياب الطلاب</p>
                    <a href="{{ route('attendances.index') }}" class="btn btn-outline-danger fw-bold w-100 mt-auto">الانتقال للحضور</a>
                </div>
            </div>

            <!-- كارت المدفوعات والأقساط (مضاف حديثاً) -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                <div class="card card-custom p-4 bg-white text-center h-100">
                    <i class="fa-solid fa-receipt text-success fs-1 mb-3"></i>
                    <h5 class="fw-bold">المدفوعات والأقساط</h5>
                    <p class="text-muted small">متابعة دفعات الطلاب ورسوم الدورات وإصدار السندات</p>
                    <a href="/api/payments" class="btn btn-outline-success fw-bold w-100 mt-auto">الانتقال للمدفوعات</a>
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
                <!-- زر تسجيل دفعة جديدة (مضاف حديثاً) -->
                <a href="/api/payments" class="btn btn-sm btn-outline-success fw-bold"><i class="fa-solid fa-plus me-1"></i> تسجيل دفعة جديدة</a>
            </div>
        </div>
    </div>

</body>
</html>