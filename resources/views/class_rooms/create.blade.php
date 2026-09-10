<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة قاعة جديدة - EduPulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark m-0">إضافة قاعة دراسية جديدة</h3>
            <a href="{{ route('class-rooms.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-right me-1"></i> رجوع للقاعات
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-custom p-4 bg-white">
            <form action="{{ route('class-rooms.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">اسم القاعة / الرقم</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="مثال: القاعة 101 أو المقتطف A" value="{{ old('name') }}" required>
                </div>

                <div class="mb-4">
                    <label for="capacity" class="form-label fw-bold">السعة الاستيعابية (عدد الطلاب)</label>
                    <input type="number" name="capacity" id="capacity" class="form-control" placeholder="مثال: 30" value="{{ old('capacity') }}" min="1" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold w-100">
                        <i class="fa-solid fa-check me-1"></i> حفظ القاعة
                    </button>
                    <a href="{{ route('class-rooms.index') }}" class="btn btn-light fw-bold w-100">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>