<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet; // أضف هذا السطر مع الـ imports في الأعلى
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; // تم إضافة كلاس حماية كلمة المرور هنا

class AuthController extends Controller
{
    // دالة إنشاء الحساب
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // إجبار المستخدم على صيغة إيميل صحيحة (يجب أن يحوي @ ونطاق صالح)
            'email' => 'required|string|email|max:255|unique:users',
            // شروط كلمة المرور: 7 أحرف على الأقل، حروف، أرقام، رموز، مع التأكيد
            'password' => [
                'required',
                'confirmed',
                Password::min(7)
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
            'phone' => 'required|string',
            'role' => 'nullable|string|in:donor,organization,agent,admin', // << لازم يكون هاد السطر موجود
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'] ?? 'donor', // أمان إضافي لإعطاء دور افتراضي
        ]);

        // ---  : إنشاء محفظة صفرية للمستخدم فوراً ---
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0.00,
        ]);
        // --------------------------------------------------------

        $token = $user->createToken('AtaaToken')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء الحساب والمحفظة بنجاح',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // دالة تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $user->createToken('AtaaToken')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // دالة تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }
}
