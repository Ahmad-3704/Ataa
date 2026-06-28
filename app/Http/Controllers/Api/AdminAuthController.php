<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // 1. إنشاء حساب مدير جديد (للتجربة)
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
            'is_super_admin' => 'nullable|boolean',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_super_admin' => $validated['is_super_admin'] ?? false,
        ]);

        $token = $admin->createToken('AdminAtaaToken')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء حساب المدير بنجاح',
            'admin' => $admin,
            'token' => $token
        ], 201);
    }

    // 2. تسجيل دخول المدير
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'بيانات دخول المدير غير صحيحة'], 401);
        }

        $token = $admin->createToken('AdminAtaaToken')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل دخول المدير بنجاح',
            'admin' => $admin,
            'token' => $token
        ], 200);
    }

    // 3. تسجيل خروج المدير
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'تم تسجيل خروج المدير بنجاح'], 200);
    }
}
