<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentTask;
use App\Models\Notification;
use Illuminate\Http\Request;

class AgentTaskController extends Controller
{
    /**
     * جلب المهام الخاصة بالمندوب الحالي
     */
    public function index(Request $request)
    {
        $tasks = AgentTask::query()
            ->where('agent_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المهام بنجاح',
            'data' => $tasks
        ]);
    }

    /**
     * مسح QR Code لتأكيد إنجاز المهمة
     */
    public function scanQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        // البحث عن المهمة بناءً على الـ QR Code الخاص بها، والتأكد أنها تابعة لهذا المندوب
        $task = AgentTask::query()
            ->where('qr_code', $request->qr_code)
            ->where('agent_id', $request->user()->id)
            ->first();

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'الرمز غير صحيح أو المهمة لا تخصك'
            ], 404);
        }

        if ($task->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'تم إنجاز هذه المهمة مسبقاً'
            ], 400);
        }

        // تحديث حالة المهمة
        $task->update(['status' => 'completed']);

        // (اختياري) إرسال إشعار للمندوب بنجاح العملية
        Notification::create([
            'user_id' => $request->user()->id,
            'title' => 'تم إنجاز المهمة',
            'message' => "تم تأكيد إنجاز المهمة: {$task->title}"
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تأكيد إنجاز المهمة بنجاح!',
            'data' => $task
        ]);
    }
}
