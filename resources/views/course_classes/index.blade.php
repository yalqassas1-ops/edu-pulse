<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الشُعب الدراسية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-gauge me-1"></i> لوحة التحكم
                    </a>
                </div>

            </div>
        </div>

        <!-- Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
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
                                        <button class="btn btn-sm btn-outline-danger btn-custom" title="حذف" onclick="return confirm('هل أنت تأكد من حذف هذه الشعبة؟')">
                                            <i class="fa-solid fa-trash-can"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>