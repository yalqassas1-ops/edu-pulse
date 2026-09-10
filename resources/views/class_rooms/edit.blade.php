<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل القاعة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 500px;">
        <div class="card p-4 shadow-sm border-0">
            <h4 class="mb-3 fw-bold">تعديل بيانات القاعة</h4>
            <form action="{{ route('class-rooms.update', $classRoom->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم القاعة</label>
                    <input type="text" name="name" value="{{ $classRoom->name }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">السعة الاستيعابية</label>
                    <input type="number" name="capacity" value="{{ $classRoom->capacity }}" class="form-control" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">تحديث</button>
                    <a href="{{ route('class-rooms.index') }}" class="btn btn-light w-100">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>