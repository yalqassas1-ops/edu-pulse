<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة محاضر جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 650px;">
        <div class="card card-custom p-4">
            <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-user-plus text-success me-2"></i> إضافة محاضر جديد</h4>

            <form action="{{ route('teachers.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">اسم المحاضر <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="مثال: د. أحمد الخطيب">
                </div>

                <div class="mb-3">
                    <label for="specialization" class="form-label fw-semibold">التخصص <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="specialization" name="specialization" required placeholder="مثال: تطوير برمجيات / هندسة حاسوب">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                </div>

                <div class="mb-4">
                    <label for="phone" class="form-label fw-semibold">رقم الهاتف (اختياري)</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="0590000000">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-success">حفظ بيانات المحاضر</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>