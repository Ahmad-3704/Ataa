<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // لا تنسَ هذا السطر لتشفير كلمة المرور

class ProfileController extends Controller
{
    /**
     * استكمال بيانات الملف الشخصي بناءً على دور المستخدم
     */
    public function completeProfile(Request $request)
    {
        $user = $request->user(); // جلب المستخدم المسجل دخوله حالياً

        // 1. إذا كان المستخدم "جمعية"
        if ($user->role === 'organization') {
            $validated = $request->validate([
                'license_number' => 'required|string|unique:organizations,license_number,' . optional($user->organization)->id,
                'address' => 'required|string',
                'description' => 'nullable|string',
            ]);
            $user->organization()->updateOrCreate(['user_id' => $user->id], $validated);
        }

        // 2. إذا كان المستخدم "مستفيد"
        elseif ($user->role === 'beneficiary') {
            $validated = $request->validate([
                'national_id' => 'required|string|unique:beneficiaries,national_id,' . optional($user->beneficiary)->id,
                'address' => 'nullable|string',
                'family_members_count' => 'required|integer|min:1',
            ]);
            $user->beneficiary()->updateOrCreate(['user_id' => $user->id], $validated);
        }

        // 3. إذا كان المستخدم "مندوب"
        elseif ($user->role === 'agent') {
            $validated = $request->validate([
                'vehicle_type' => 'required|string',
                'vehicle_number' => 'nullable|string',
            ]);
            $user->agent()->updateOrCreate(['user_id' => $user->id], $validated);
        }

        // 4. إذا كان المستخدم "متبرع"
        elseif ($user->role === 'donor') {
            $validated = $request->validate([
                'is_anonymous' => 'required|boolean',
            ]);
            $user->donor()->updateOrCreate(['user_id' => $user->id], $validated);
        }

        // جلب المستخدم مع بيانات البروفايل الجديد الخاص به لعرضها في الرد
        $user->load($user->role);

        return response()->json([
            'message' => 'تم تحديث واستكمال بيانات الملف الشخصي بنجاح',
            'user' => $user
        ], 200);
    }

    /**
     * تحديث البيانات الأساسية (الاسم والبريد الإلكتروني لأي مستخدم)
     */
    public function updateInfo(Request $request)
    {
        $user = $request->user();

        // التحقق من البيانات (نسمح بتحديث الاسم أو الإيميل، ونتأكد أن الإيميل غير مستخدم لغيره)
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث البيانات الشخصية الأساسية بنجاح.',
            'data' => clone $user
        ], 200);
    }

    /**
     * تغيير كلمة المرور لأي مستخدم
     */
    public function updatePassword(Request $request)
    {
        // التحقق من كلمة المرور الحالية والجديدة
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // تشفير وتحديث كلمة المرور
        $request->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ], 200);
    }
}
