<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المحاضرين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 6px 14px; font-weight: 500; }
        .badge-spec { background-color: #e0e7ff; color: #3730a3; font-weight: 600; padding: 6px 12px; border-radius: 20px; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container">
        <!-- Header Section -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-chalkboard-user text-success me-2"></i> إدارة المحاضرين والمعلمين
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتعديل بيانات الهيئة التدريسية في المركز</p>
                </div>
                <!-- الأزرار الأساسية فقط -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('teachers.create') }}" class="btn btn-success btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-user-plus me-2"></i> إضافة محاضر جديد
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-gauge me-2"></i> لوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Notification -->
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
                            <th class="py-3 text-start">اسم المحاضر</th>
                            <th class="py-3">التخصص</th>
                            <th class="py-3">البريد الإلكتروني</th>
                            <th class="py-3">رقم الهاتف</th>
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
                            <td>
                                <span class="badge-spec">
                                    <i class="fa-solid fa-briefcase me-1"></i>{{ $teacher->specialization }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $teacher->email }}</td>
                            <td class="text-muted dir-ltr">{{ $teacher->phone ?? 'غير مدخل' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-warning btn-custom" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger btn-custom" title="حذف" onclick="return confirm('هل أنت تأكد من حذف هذا المحاضر؟')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-muted">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>