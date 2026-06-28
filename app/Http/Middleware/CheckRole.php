<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * معالجة الطلب القادم.
     * هنا أضفنا بارامتر ثالث اسمه $role وهو الدور الذي سنمرره من ملف المسارات
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. التأكد أولاً أن المستخدم مسجل دخول
        if (!$request->user()) {
            return response()->json(['message' => 'غير مصرح لك، يرجى تسجيل الدخول أولاً'], 401);
        }

        // 2. التحقق من أن دور المستخدم يطابق الدور المطلوب في المسار
        if ($request->user()->role !== $role) {
            return response()->json([
                'message' => 'عذراً، لا تملك الصلاحيات الكافية للوصول إلى هذا القسم'
            ], 403); // 403 تعني Forbidden (ممنوع)
        }

        // إذا نجح في الاختبارين، نسمح للطلب بالمرور إلى الكنترولر
        return $next($request);
    }
}
