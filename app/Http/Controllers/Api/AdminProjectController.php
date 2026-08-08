<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
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
// إرسال إشعار للجمعية صاحبة المشروع
        $statusAr = $request->status == 'approved' ? 'الموافقة على' : 'رفض';
        // ملاحظة: استبدل organization_id بالحقل الذي يربط المشروع بالجمعية في قاعدة بياناتك (مثلاً user_id)
        $orgUserId = $project->organization->user_id ?? $project->organization_id;

        Notification::create([
            'user_id' => $orgUserId,
            'title' => "تحديث حالة المشروع",
            'message' => "تم $statusAr مشروعك '{$project->title}' من قبل الإدارة."
        ]);
        $statusMsg = $request->status == 'approved' ? 'مقبول' : 'مرفوض';

        return response()->json([
            'status' => 'success',
            'message' => "تم تحديث حالة المشروع بنجاح لتصبح: $statusMsg",
            'data' => $project
        ]);
    }
}
