<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل سجل الحضور - EduPulse</title>
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
                    <i class="fa-solid fa-user-pen text-warning me-2"></i> تعديل حضور طالب
                </h4>
                <a href="{{ route('attendances.index', ['course_class_id' => $attendance->course_class_id, 'date' => $attendance->date]) }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="fa-solid fa-arrow-right me-1"></i> عودة
                </a>
            </div>

            <!-- تفاصيل الطالب والشعبة -->
            <div class="bg-light p-3 rounded-3 mb-4">
                <div class="row g-2 text-secondary small">
                    <div class="col-6"><strong>الطالب:</strong> <span class="text-dark fw-bold">{{ $attendance->student->name ?? 'غير محدد' }}</span></div>
                    <div class="col-6"><strong>الشعبة:</strong> <span class="text-dark fw-bold">{{ $attendance->courseClass->class_number ?? $attendance->course_class_id }}</span></div>
                    <div class="col-12 mt-2"><strong>الدورة:</strong> <span class="text-dark fw-bold">{{ $attendance->courseClass->course->title ?? '-' }}</span></div>
                </div>
            </div>

            <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- التاريخ -->
                <div class="mb-3">
                    <label for="date" class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $attendance->date) }}" required>
                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- حالة الحضور -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">حالة الحضور <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="status" id="status_present" value="present" {{ old('status', $attendance->status) == 'present' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success py-2" for="status_present">
                            <i class="fa-solid fa-check me-1"></i> حاضر
                        </label>

                        <input type="radio" class="btn-check" name="status" id="status_absent" value="absent" {{ old('status', $attendance->status) == 'absent' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger py-2" for="status_absent">
                            <i class="fa-solid fa-xmark me-1"></i> غائب
                        </label>

                        <input type="radio" class="btn-check" name="status" id="status_late" value="late" {{ old('status', $attendance->status) == 'late' ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning py-2" for="status_late">
                            <i class="fa-regular fa-clock me-1"></i> متأخر
                        </label>
                    </div>
                    @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- ملاحظات -->
                <div class="mb-4">
                    <label for="notes" class="form-label fw-bold">ملاحظات (اختياري)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="أدخل أي ملاحظة بخصوص التأخير أو الغياب...">{{ old('notes', $attendance->notes) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('attendances.index', ['course_class_id' => $attendance->course_class_id, 'date' => $attendance->date]) }}" class="btn btn-light btn-custom">إلغاء</a>
                    <button type="submit" class="btn btn-warning btn-custom text-dark fw-bold">
                        <i class="fa-solid fa-pen-to-square me-1"></i> تحديث السجل
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>