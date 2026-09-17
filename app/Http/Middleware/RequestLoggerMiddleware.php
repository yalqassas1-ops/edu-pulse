<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // 👈 إضافة هذا السطر
use Symfony\Component\HttpFoundation\Response;

class RequestLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. تسجيل وقت بداية الطلب
        $startTime = microtime(true);

        // 2. تنفيذ الطلب والانتقال للخطوة التالية
        $response = $next($request);

        // 3. حساب وقت التنفيذ المتبقي بالميلي ثانية
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        // 4. تسجيل بيانات الطلب التلقائية (المرحلة الثالثة)
        Log::info('تتبع الطلب التلقائي', [
            'method'         => $request->method(),          // نوع الطلب (GET, POST, ...)
            'url'            => $request->fullUrl(),         // الرابط المستهدف (URL)
            'execution_time' => $executionTime . ' ms',     // الوقت الاستغراقي بالميلي ثانية
            'ip'             => $request->ip(),             // عنوان الـ IP
            'user_id'        => Auth::id() ?? 'Guest',       // 👈 استبدلت بـ Auth::id() لإخفاء الخط الأحمر
        ]);

        return $response;
    }
}