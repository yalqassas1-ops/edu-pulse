<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - EduPulse</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; }
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; direction: rtl; }
        .register-card { background: #ffffff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); width: 100%; max-width: 420px; border: 1px solid #e2e8f0; }
        .brand-header { text-align: center; margin-bottom: 1.5rem; }
        .brand-header .icon-wrapper { background-color: #eff6ff; color: #2563eb; width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; font-size: 1.8rem; }
        .brand-header h2 { color: #1e293b; font-size: 1.5rem; font-weight: 700; }
        .brand-header p { color: #64748b; font-size: 0.875rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 0.75rem 2.6rem 0.75rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; }
        input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        .btn-submit { width: 100%; padding: 0.85rem; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1rem; }
        .btn-submit:hover { background-color: #1d4ed8; }
        .login-footer { margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 1rem; }
        .login-footer a { color: #2563eb; font-weight: 600; text-decoration: none; }
        .alert-danger { background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1.25rem; }
    </style>
</head>
<body>

<div class="register-card">
    <div class="brand-header">
        <div class="icon-wrapper">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h2>EduPulse</h2>
        <p>إنشاء حساب جديد بالنظام</p>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('register.submit') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">الاسم الكامل</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسمك الكامل" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="user@domain.com" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-shield-halved"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <span>إنشاء الحساب والدخول</span>
            <i class="fa-solid fa-arrow-left"></i>
        </button>
    </form>

    <div class="login-footer">
        <span>لديك حساب بالفعل؟</span>
        <a href="{{ route('login') }}">تسجيل الدخول</a>
    </div>
</div>

</body>
</html>