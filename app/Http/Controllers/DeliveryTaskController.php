<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryTask;
use App\Models\User;
use App\Models\Notification; // تم إضافة المودل الخاص بإشعاراتك المحلية

class DeliveryTaskController extends Controller
{
    // 1. دالة إسناد المهمة للمندوب (تستخدمها الإدارة)
    public function assignTask(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'agent_id' => 'required|exists:users,id',
        ]);

        // التأكد أن المستخدم المختار هو مندوب فعلاً
        $agent = User::query()->find($request->agent_id);
        if ($agent->role !== 'agent') {
            return response()->json(['error' => 'المستخدم المحدد ليس مندوباً'], 400);
        }

        // إنشاء المهمة (الـ Token سيتولد تلقائياً بفضل الموديل)
        $task = DeliveryTask::create([
            'project_id' => $request->project_id,
            'agent_id' => $request->agent_id,
            'status' => 'pending',
        ]);

        // 1. توثيق الإشعار في نظامك المحلي
        Notification::create([
            'user_id' => $agent->id,
            'title' => 'مهمة توصيل جديدة!',
            'message' => "تم تكليفك بمهمة توصيل جديدة للمشروع رقم {$task->project_id}."
        ]);

        // 2. إرسال الإشعار عبر نظام لارافيل للمندوب
        $agent->notify(new \App\Notifications\NewTaskNotification($task));

        return response()->json([
            'message' => 'تم إسناد المهمة للمندوب بنجاح',
            'data' => $task
        ]);
    }

    // 2. دالة تأكيد التسليم (يستخدمها المندوب عند مسح الـ QR Code)
    public function confirmDelivery(Request $request)
    {
        $request->validate([
            'qr_code_token' => 'required|string'
        ]);

        // البحث عن المهمة المرتبطة بهذا الرمز السري
        $task = DeliveryTask::where('qr_code_token', $request->qr_code_token)->first();

        if (!$task) {
            return response()->json(['error' => 'الرمز غير صحيح أو المهمة غير موجودة'], 404);
        }

        if ($task->status === 'delivered') {
            return response()->json(['message' => 'هذه المهمة تم تسليمها مسبقاً'], 400);
        }

        // تحديث حالة المهمة إلى "تم التسليم"
        $task->update([
            'status' => 'delivered'
        ]);

        return response()->json([
            'message' => 'تم تأكيد التسليم بنجاح!',
            'data' => $task
        ]);
    }
}
