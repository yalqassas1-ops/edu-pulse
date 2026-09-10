<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة القاعات الدراسية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        القاعات الدراسية <i class="fa-solid fa-door-open text-primary ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">عرض وتعديل كافة القاعات وسعتها الاستيعابية</p>
                </div>

                <!-- Action Buttons (اليسار - نفس نمط باقي الصفحات) -->
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('class-rooms.create') }}" class="btn btn-primary btn-custom shadow-sm">
                        <i class="fa-solid fa-plus me-1"></i> إضافة قاعة جديدة
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom shadow-sm">
                        <i class="fa-solid fa-gauge me-1"></i> لوحة التحكم
                    </a>
                </div>

            </div>
        </div>

        <!-- Alerts -->
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
                            <th class="py-3">اسم القاعة</th>
                            <th class="py-3">السعة الاستيعابية</th>
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
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('class-rooms.edit', $classRoom->id) }}" class="btn btn-sm btn-outline-warning btn-custom" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('class-rooms.destroy', $classRoom->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger btn-custom" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذه القاعة؟')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-muted">
                                <i class="fa-solid fa-door-closed fa-2x mb-3 d-block text-secondary"></i>
                                لا توجد قاعات دراسية مضافة حتى الآن.
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