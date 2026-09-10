<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة شعبة جديدة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 8px 20px; font-weight: 500; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 900px;">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">
                    إضافة شعبة دراسية جديدة <i class="fa-solid fa-plus-circle ms-2" style="color: #7c3aed;"></i>
                </h4>
                <a href="{{ route('course-classes.index') }}" class="btn btn-outline-secondary btn-custom">
                    <i class="fa-solid fa-arrow-right me-1"></i> عودة للشُعب
                </a>
            </div>
        </div>

        <div class="card card-custom p-4">
            <form action="{{ route('course-classes.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">رقم الشعبة</label>
                        <input type="text" name="class_number" class="form-control" placeholder="مثال: CS-101" value="{{ old('class_number') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">الدورة التدريبية</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">اختر الدورة...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">المحاضر</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">اختر المحاضر...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">القاعة الدراسية</label>
                        <select name="class_room_id" class="form-select" required>
                            <option value="">اختر القاعة...</option>
                            @foreach($classRooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ البداية</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">تاريخ النهاية</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold d-block">أيام المحاضرات</label>
                        <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
                            @foreach(['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'] as $day)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="{{ $day }}" id="day_{{ $loop->index }}">
                                    <label class="form-check-label" for="day_{{ $loop->index }}">{{ $day }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">وقت البداية</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">وقت النهاية</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">حالة الشعبة</label>
                        <select name="status" class="form-select" required>
                            <option value="upcoming">قادمة</option>
                            <option value="ongoing">جارية</option>
                            <option value="completed">مكتملة</option>
                        </select>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-custom shadow-sm" style="background-color: #7c3aed; border: none;">
                        <i class="fa-solid fa-save me-1"></i> حفظ الشعبة
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>