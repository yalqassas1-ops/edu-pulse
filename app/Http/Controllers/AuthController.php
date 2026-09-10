<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // 1. استدعاء الـ Log
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    // 1. تسجيل حساب جديد (Register)
    public function register(Request $request)
    {
        // المرحلة الأولى: استخدام try ... catch لمعالجة الأخطاء والتسجيل المباشر
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            // المرحلة الأولى: عند النجاح سجل INFO
            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (ValidationException $e) {
            throw $e; // نترك خطأ التحقق للارفيل كالعادة
        } catch (Exception $e) {
            // المرحلة الأولى: عند الفشل سجل ERROR
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'user_id' => null
            ]);

            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // 2. تسجيل الدخول (Login)
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            // المرحلة الثانية: التحقق من كلمة السر والتسجيل كـ WARNING عند الخطأ
            if (! $user || ! Hash::check($request->password, $user->password)) {

                // تنبيه أمني: نسجل الـ Email والـ IP فقط (بدون كلمة السر)
                Log::warning('Failed login attempt', [
                    'email' => $request->email,
                    'ip' => $request->ip()
                ]);

                throw ValidationException::withMessages([
                    'email' => ['بيانات الدخول غير صحيحة.'],
                ]);
            }

            // حذف التوكنات القديمة وتوليد توكن جديد
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            // المرحلة الأولى: عند نجاح الدخول نسجل INFO
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            // المرحلة الأولى: عند حدوث أي استثناء أو خطأ سيرفر نسجل ERROR
            Log::error('Login process encountered an error', [
                'error' => $e->getMessage(),
                'user_id' => null
            ]);

            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // 3. تسجيل الخروج (Logout)
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        Log::info('User logged out', ['user_id' => $user->id]);

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    // 4. جلب بيانات المستخدم الحالي (Profile)
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}