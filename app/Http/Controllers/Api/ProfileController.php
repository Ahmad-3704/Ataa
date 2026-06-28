<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
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
            // updateOrCreate: إذا كان عنده بروفايل بيعدله، وإذا ما عنده بينشئ واحد جديد
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
}
