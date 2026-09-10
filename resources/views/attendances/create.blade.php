<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حضور طالب - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-custom { border-radius: 8px; padding: 8px 18px; font-weight: 500; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 600px;">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user-check text-success me-2"></i> تسجيل حضور طالب منفرد
                </h4>
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="fa-solid fa-arrow-right me-1"></i> عودة
                </a>
            </div>

            <form action="{{ route('attendances.store') }}" method="POST">
                @csrf

                <!-- اختيار الشعبة -->
                <div class="mb-3">
                    <label for="course_class_id" class="form-label fw-bold">الشعبة الدراسية <span class="text-danger">*</span></label>
                    <select name="course_class_id" id="course_class_id" class="form-select @error('course_class_id') is-invalid @enderror" required>
                        <option value="" selected disabled>-- اختر الشعبة --</option>
                        @foreach($courseClasses as $class)
                            <option value="{{ $class->id }}" {{ old('course_class_id') == $class->id ? 'selected' : '' }}>
                                شعبة: {{ $class->class_number ?? $class->id }} - الدورة: {{ $class->course->title ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- اختيار الطالب -->
                <div class="mb-3">
                    <label for="student_id" class="form-label fw-bold">الطالب <span class="text-danger">*</span></label>
                    <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="" selected disabled>-- اختر الطالب --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- التاريخ -->
                <div class="mb-3">
                    <label for="date" class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- حالة الحضور -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">حالة الحضور <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="status" id="status_present" value="present" {{ old('status', 'present') == 'present' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success py-2" for="status_present">
                            <i class="fa-solid fa-check me-1"></i> حاضر
                        </label>

                        <input type="radio" class="btn-check" name="status" id="status_absent" value="absent" {{ old('status') == 'absent' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger py-2" for="status_absent">
                            <i class="fa-solid fa-xmark me-1"></i> غائب
                        </label>

                        <input type="radio" class="btn-check" name="status" id="status_late" value="late" {{ old('status') == 'late' ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning py-2" for="status_late">
                            <i class="fa-regular fa-clock me-1"></i> متأخر
                        </label>
                    </div>
                    @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- ملاحظات -->
                <div class="mb-4">
                    <label for="notes" class="form-label fw-bold">ملاحظات (اختياري)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="أدخل أي ملاحظات إن وجدت...">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('attendances.index') }}" class="btn btn-light btn-custom">إلغاء</a>
                    <button type="submit" class="btn btn-success btn-custom">
                        <i class="fa-solid fa-floppy-disk me-1"></i> حفظ السجل
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>