<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Models\User;
use App\Notifications\LoginSuccessNotification;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل دخول مدير النظام
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة بيانات تسجيل الدخول
     */
    public function login(Request $request)
    {
        // 1. التحقق من صحة المدخلات
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'الرجاء إدخال البريد الإلكتروني.',
            'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'الرجاء إدخال كلمة المرور.',
        ]);

        try {
            // 2. محاولة تسجيل الدخول
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                // إعادة إنشاء الـ Session للحماية من ثغرات Session Fixation
                $request->session()->regenerate();

                // 🟢 تسجيل نجاح عملية الدخول (INFO Log)
                Log::info('تم تسجيل الدخول بنجاح', [
                    'user_id' => Auth::id(),
                    'email'   => $request->email,
                    'ip'      => $request->ip(),
                ]);

                // 📧 حماية إرسال الإشعار والـ Broadcasting حتى لا ينهار النظام عند فشل خادم البث
                try {
                    $user = Auth::user();
                    $time = now()->format('Y-m-d H:i:s');
                    $ip   = $request->ip();

                    // إرسال إشعار الدخول بنجاح
                    $user->notify(new LoginSuccessNotification($time, $ip));
                } catch (Exception $notificationException) {
                    // تسجيل فشل البث/البريد في الـ Log دون منع المستخدم من الدخول
                    Log::error('فشل في إرسال إشعار تسجيل الدخول عبر البث/البريد', [
                        'user_id' => Auth::id(),
                        'error'   => $notificationException->getMessage()
                    ]);
                }

                // التوجيه إلى لوحة التحكم الرئيسية
                return redirect()->intended('/dashboard');
            }

            // 🟡 تتبع محاولات الاختراق - كلمة سر خاطئة (Security WARNING Log)
            Log::warning('محاولة تسجيل دخول فاشلة - كلمة سر خاطئة', [
                'email' => $request->email,
                'ip'    => $request->ip(),
            ]);

            // 3. في حال فشل بيانات الدخول
            return back()->withErrors([
                'email' => 'بيانات الدخول غير صحيحة، يرجى التأكد من البريد وكلمة المرور.',
            ])->onlyInput('email');

        } catch (Exception $e) {
            // 🔴 تسجيل وقوع خطأ غير متوقع بالنظام (ERROR Log)
            Log::error('حدث خطأ غير متوقع أثناء عملية تسجيل الدخول', [
                'error_message' => $e->getMessage(),
                'user_id'       => Auth::id() ?? null,
                'ip'            => $request->ip(),
            ]);

            return back()->withErrors([
                'email' => 'حدث خطأ غير متوقع في النظام، يرجى المحاولة لاحقاً.',
            ])->onlyInput('email');
        }
    }

    /**
     * عرض صفحة إنشاء حساب جديد (الخاصة بالزبائن)
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * معالجة بيانات إنشاء حساب جديد
     */
    public function register(Request $request)
    {
        // 1. التحقق من صحة المدخلات
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'الرجاء إدخال الاسم كامل.',
            'email.required'     => 'الرجاء إدخال البريد الإلكتروني.',
            'email.email'        => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique'       => 'هذا البريد الإلكتروني مُسجل مسبقاً.',
            'password.required'  => 'الرجاء إدخال كلمة المرور.',
            'password.min'       => 'كلمة المرور يجب أن لا تقل عن 8 خانات.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        try {
            // 2. إنشاء المستخدم الجديد وتشفير كلمة المرور
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 🟢 تسجيل عملية إنشاء الحساب (INFO Log)
            Log::info('تم إنشاء حساب جديد بنجاح', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'ip'      => $request->ip(),
            ]);

            // 3. تسجيل دخول المستخدم آلياً بعد الإنشاء مباشرة
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'تم إنشاء الحساب وتسجيل الدخول بنجاح!');

        } catch (Exception $e) {
            // 🔴 تسجيل وقوع خطأ أثناء إنشاء الحساب
            Log::error('حدث خطأ أثناء عملية إنشاء الحساب', [
                'error_message' => $e->getMessage(),
                'email'         => $request->email,
                'ip'            => $request->ip(),
            ]);

            return back()->withErrors([
                'email' => 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقاً.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        // تسجيل عملية الخروج لأغراض المتابعة بالأمان
        if (Auth::check()) {
            Log::info('تم تسجيل الخروج', [
                'user_id' => Auth::id(),
                'ip'      => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}