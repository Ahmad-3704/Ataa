<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * جلب قائمة بأكثر المتبرعين
     */
    public function topDonors()
    {
        // 1. جلب المتبرعين مع مجموع تبرعاتهم باستخدام دالة withSum السحرية
        $topDonors = User::query()->where('role', 'donor')
            ->whereHas('donor') // التأكد من أن المستخدم لديه بروفايل متبرع
            ->withSum('donations', 'amount') // ستقوم بجمع حقل amount من علاقة donations
            ->orderBy('donations_sum_amount', 'desc') // الترتيب التنازلي حسب المجموع
            ->take(10) // جلب أعلى 10 متبرعين فقط
            ->get();

        // 2. تغليف البيانات وإخفاء هوية المتبرعين السريين
        $formattedDonors = $topDonors->map(function ($user) {
            $isAnonymous = $user->donor->is_anonymous ?? false;

            return [
                'donor_name' => $isAnonymous ? 'فاعل خير' : $user->name,
                'total_donated' => $user->donations_sum_amount ?? 0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب لوحة الصدارة بنجاح',
            'data' => $formattedDonors
        ]);
    }
}
