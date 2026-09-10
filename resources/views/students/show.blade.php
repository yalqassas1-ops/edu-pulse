<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطالب - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-3 p-md-5">

    <div class="container" style="max-width: 650px;">
        <div class="card card-custom p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h4 class="fw-bold text-dark m-0">
                    <i class="fa-solid fa-id-card text-info me-2"></i>تفاصيل الطالب
                </h4>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-right me-1"></i> العودة للقائمة
                </a>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="text-muted d-block small mb-1">الاسم الكامل</label>
                    <div class="fw-bold fs-5 text-dark">{{ $student->name }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted d-block small mb-1">البريد الإلكتروني</label>
                    <div class="fw-semibold text-dark">{{ $student->email }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted d-block small mb-1">رقم الهاتف</label>
                    <div class="fw-semibold text-dark">{{ $student->phone ?? 'غير محدد' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted d-block small mb-1">الجنس</label>
                    <div>
                        @if(in_array(strtolower($student->gender ?? ''), ['female', 'f', 'أنثى']))
                            <span class="badge bg-danger p-2"><i class="fa-solid fa-venus me-1"></i> أنثى</span>
                        @else
                            <span class="badge bg-primary p-2"><i class="fa-solid fa-mars me-1"></i> ذكر</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted d-block small mb-1">تاريخ التسجيل بالنظام</label>
                    <div class="text-muted small">{{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير محدد' }}</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning text-white fw-bold">
                    <i class="fa-solid fa-pen-to-square me-1"></i> تعديل
                </a>
                <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fa-solid fa-trash me-1"></i> حذف
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>