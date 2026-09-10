<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة دورة جديدة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 700px;">
        <div class="card card-custom p-4">
            <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-plus text-primary me-2"></i> إضافة دورة تدريبية جديدة</h4>

            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">عنوان الدورة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="مثال: دورة الذكاء الاصطناعي" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-semibold">التصنيف <span class="text-danger">*</span></label>
                        <select class="form-select no-nice-select" id="category_id" name="category_id" required>
                            <option value="" selected disabled>-- اختر التصنيف --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label fw-semibold">المحاضر المسئول</label>
                        <select class="form-select no-nice-select" id="teacher_id" name="teacher_id">
                            <option value="" selected>-- بدون محاضر --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->specialization }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label fw-semibold">السعر ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="150" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="total_hours" class="form-label fw-semibold">عدد الساعات <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_hours" name="total_hours" placeholder="40" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ الدورة</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>