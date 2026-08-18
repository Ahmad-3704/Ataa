<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
use App\Models\User; // تم إضافة مودل المستخدم لكي نرسل له الإشعار
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class AdminProjectController extends Controller
{
    /**
     * جلب جميع المشاريع المعلقة (بانتظار الموافقة)
     */
    public function pendingProjects()
    {
        // جلب المشاريع اللي حالتها pending فقط مع بيانات الجمعية
        $projects = Project::query()
            ->where('status', 'pending')
            ->with('organization.user:id,name,profile_image')
            ->orderBy('created_at', 'asc') // الأقدم فالأحدث
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المشاريع المعلقة بنجاح',
            'data' => $projects
        ]);
    }

    /**
     * تحديث حالة المشروع (قبول أو رفض)
     */
    public function updateStatus(Request $request, $id)
    {
        // التحقق من أن الحالة المرسلة إما مقبولة أو مرفوضة
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $project = Project::query()->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'المشروع غير موجود.'
            ], 404);
        }

        // تحديث الحالة
        $project->status = $request->status;
        $project->save();

        // إعداد نصوص الإشعار
        $statusAr = $request->status == 'approved' ? 'الموافقة على' : 'رفض';
        $orgUserId = $project->organization->user_id ?? $project->organization_id;

        // 1. نظام الإشعارات المحلي الخاص بك (تم الاحتفاظ به)
        Notification::create([
            'user_id' => $orgUserId,
            'title' => "تحديث حالة المشروع",
            'message' => "تم $statusAr مشروعك '{$project->title}' من قبل الإدارة."
        ]);

        // 2. نظام إشعارات لارافيل (الإضافة الجديدة)
        $organizationUser = User::query()->find($orgUserId);
        if ($organizationUser) {
            $organizationUser->notify(new \App\Notifications\ProjectStatusNotification($project, $statusAr));
        }

        $statusMsg = $request->status == 'approved' ? 'مقبول' : 'مرفوض';

        return response()->json([
            'status' => 'success',
            'message' => "تم تحديث حالة المشروع بنجاح لتصبح: $statusMsg",
            'data' => $project
        ]);
    }
}
