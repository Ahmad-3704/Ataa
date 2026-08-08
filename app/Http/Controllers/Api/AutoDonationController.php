<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoDonation;
use Illuminate\Http\Request;

class AutoDonationController extends Controller
{
    /**
     * عرض إعدادات التبرع التلقائي الحالية للمستخدم
     */
    public function index(Request $request)
    {
        $autoDonation = AutoDonation::query()->where('user_id', $request->user()->id)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب إعدادات التبرع التلقائي',
            'data' => $autoDonation
        ]);
    }

    /**
     * تفعيل أو تحديث التبرع التلقائي
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'interval' => 'required|in:daily,weekly,monthly',
        ]);

        $user = $request->user();

        // استخدام updateOrCreate لحفظ إعداد واحد فقط لكل مستخدم (تجنباً للتكرار)
        $autoDonation = AutoDonation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'amount' => $request->amount,
                'interval' => $request->interval,
                'status' => 'active'
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إعداد التبرع التلقائي بنجاح!',
            'data' => $autoDonation
        ]);
    }

    /**
     * إيقاف أو تشغيل التبرع التلقائي (تغيير الحالة)
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $autoDonation = AutoDonation::query()->where('user_id', $request->user()->id)->first();

        if (!$autoDonation) {
            return response()->json([
                'status' => 'error',
                'message' => 'ليس لديك إعدادات تبرع تلقائي لتعديلها.'
            ], 404);
        }

        $autoDonation->update(['status' => $request->status]);
        $statusMsg = $request->status == 'active' ? 'مفعل' : 'موقف';

        return response()->json([
            'status' => 'success',
            'message' => "تم تغيير حالة التبرع التلقائي لتصبح: $statusMsg",
            'data' => $autoDonation
        ]);
    }
}
