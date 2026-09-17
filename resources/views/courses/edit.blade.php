<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل دورة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .preview-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 700px;">
        <div class="card card-custom p-4">
            <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-pen-to-square text-warning me-2"></i> تعديل بيانات الدورة</h4>

            <!-- 👈 إضافة enctype="multipart/form-data" لرفع الملفات -->
            <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">عنوان الدورة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $course->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-semibold">التصنيف <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label fw-semibold">المحاضر المسئول</label>
                        <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id">
                            <option value="">-- بدون محاضر --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} ({{ $teacher->specialization }})
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label fw-semibold">السعر ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $course->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="total_hours" class="form-label fw-semibold">عدد الساعات <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('total_hours') is-invalid @enderror" id="total_hours" name="total_hours" value="{{ old('total_hours', $course->total_hours) }}" required>
                        @error('total_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 👈 حقل تحديث الصورة / المرفق المعاين -->
                <div class="mb-4">
                    <label for="avatar" class="form-label fw-semibold">صورة / مرفق الدورة (اخنياري)</label>
                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*,.pdf">
                    <div class="form-text text-muted">اتركه فارغاً للحفاظ على الملف الحالي. الأنواع: (JPG, PNG, PDF).</div>
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- معاينة الملف الحالي إن وجد -->
                    @if($course->avatar)
                        <div class="mt-3 p-2 border rounded bg-light d-flex align-items-center gap-3">
                            <span class="small text-muted fw-bold">الملف الحالي:</span>
                            @if(\Illuminate\Support\Str::endsWith($course->avatar, '.pdf'))
                                <a href="{{ Storage::url($course->avatar) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-file-pdf me-1"></i> عرض ملف PDF
                                </a>
                            @else
                                <img src="{{ Storage::url($course->avatar) }}" alt="{{ $course->title }}" class="preview-img border shadow-sm">
                            @endif
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-warning text-white fw-bold">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>