<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الحضور والغياب - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        إدارة الحضور والغياب <i class="fa-solid fa-clipboard-user text-primary ms-2"></i>
                    </h3>
                    <p class="text-muted mb-0 small">رصد وتتبع حضور وغياب الطلاب حسب الشعبة واليوم</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- زر الانتقال لصفحة التقرير الشامل -->
                    <a href="{{ route('attendances.report', ['course_class_id' => $selectedClassId]) }}" class="btn btn-outline-primary btn-custom">
                        <i class="fa-solid fa-chart-pie me-1"></i> التقرير الشامل
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-custom">
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

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card card-custom p-4 mb-4">
            <form method="GET" action="{{ url('/attendances') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="course_class_id" class="form-label fw-bold">اختر الشعبة الدراسية <span class="text-danger">*</span></label>
                    <select name="course_class_id" id="course_class_id" class="form-select" required>
                        <option value="" selected disabled>-- اختر الشعبة --</option>
                        @foreach($courseClasses as $class)
                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                شعبة: {{ $class->class_number ?? $class->class_code ?? $class->id }} - الدورة: {{ $class->course->title ?? $class->course->name ?? 'غير محددة' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="date" class="form-label fw-bold">التاريخ</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}" required>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 btn-custom">
                        <i class="fa-solid fa-filter me-1"></i> عرض قائمة الطلاب
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Recording Table -->
        @if($selectedClassId)
            <div class="card card-custom overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-users text-secondary me-2"></i> قائمة الطلاب (تاريخ: {{ $selectedDate }})
                    </h5>
                </div>

                <form action="{{ url('/attendances') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="date" value="{{ $selectedDate }}">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th class="text-start">اسم الطالب</th>
                                    <th>حالة الحضور</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    @php
                                        $currentStatus = $existingAttendances[$student->id] ?? 'present';
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $loop->iteration }}</td>
                                        <td class="text-start fw-bold text-dark">{{ $student->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="attendances[{{ $student->id }}]" id="present_{{ $student->id }}" value="present" {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm" for="present_{{ $student->id }}">
                                                    <i class="fa-solid fa-check me-1"></i> حاضر
                                                </label>

                                                <input type="radio" class="btn-check" name="attendances[{{ $student->id }}]" id="absent_{{ $student->id }}" value="absent" {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm" for="absent_{{ $student->id }}">
                                                    <i class="fa-solid fa-xmark me-1"></i> غائب
                                                </label>

                                                <input type="radio" class="btn-check" name="attendances[{{ $student->id }}]" id="late_{{ $student->id }}" value="late" {{ $currentStatus == 'late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning btn-sm" for="late_{{ $student->id }}">
                                                    <i class="fa-regular fa-clock me-1"></i> متأخر
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-5 text-muted">
                                            <i class="fa-solid fa-user-slash fa-2x mb-3 d-block text-secondary"></i>
                                            لا يوجد طلاب مسجلون في هذه الشعبة حتى الآن.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($students) > 0)
                        <div class="p-3 bg-light d-flex justify-content-end border-top">
                            <button type="submit" class="btn btn-success btn-custom">
                                <i class="fa-solid fa-floppy-disk me-1"></i> حفظ الحضور والغياب
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>