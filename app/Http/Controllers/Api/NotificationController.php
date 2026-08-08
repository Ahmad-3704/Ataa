<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * جلب إشعارات المستخدم الحالي
     */
    public function index(Request $request)
    {
        // جلب الإشعارات وترتيبها من الأحدث للأقدم
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الإشعارات بنجاح',
            'data' => $notifications
        ]);
    }

    /**
     * تحديد إشعار معين كمقروء
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'الإشعار غير موجود'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديد الإشعار كمقروء',
            'data' => $notification
        ]);
    }

    /**
     * تحديد كافة إشعارات المستخدم كمقروءة
     */
    public function markAllAsRead(Request $request)
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديد كافة الإشعارات كمقروءة'
        ]);
    }
}
