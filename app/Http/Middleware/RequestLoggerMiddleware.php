<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLoggerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        // المرحلة الثالثة: تتبع الطلب والرابط والوقت المستغرق
        Log::info('Incoming HTTP Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'execution_time_ms' => $executionTime
        ]);

        return $response;
    }
}