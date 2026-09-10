<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RequestLoggerMiddleware; // 1. استدعاء الميدلوير

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 2. تفعيل الميدلوير لجميع الطلبات القادمة للمشروع
        $middleware->append(RequestLoggerMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();