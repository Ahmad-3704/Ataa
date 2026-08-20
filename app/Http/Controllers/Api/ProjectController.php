<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * استعراض جميع المشاريع المقبولة (العامة)
     */
    public function index()
    {
        $projects = Project::query()
            ->where('status', 'approved') // جلب المشاريع المقبولة فقط
            ->with('organization.user:id,name,profile_image') // جلب بيانات الجمعية والمستخدم
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المشاريع بنجاح',
            'data' => $projects
        ]);
    }

    /**
     * جلب الحالات العاجلة فقط (المسار الجديد)
     */
    public function urgent()
    {
        $urgentProjects = Project::query()
            ->where('is_urgent', true) // شرط الحالات العاجلة
            ->where('status', 'approved') // يجب أن تكون مقبولة أيضاً
            ->with('organization.user:id,name,profile_image')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الحالات العاجلة بنجاح',
            'data' => $urgentProjects
        ]);
    }

    /**
     * إضافة مشروع جديد (خاص بالجمعيات)
     */
    public function store(Request $request)
    {
        // التحقق من أن المستخدم يملك بروفايل جمعية
        if ($request->user()->role !== 'organization') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بإضافة مشاريع. هذا القسم مخصص للجمعيات فقط.'
            ], 403);
        }

        // التحقق من البيانات المدخلة
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1',
            'is_urgent' => 'sometimes|boolean', // السماح بتحديد الحالة العاجلة
        ]);

        // إنشاء المشروع (ستكون حالته الافتراضية 'pending' حسب الداتا بيز)
        $project = Project::query()->create([
            'organization_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'is_urgent' => $request->is_urgent ?? false, // حفظ الحالة العاجلة
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة المشروع بنجاح وهو بانتظار موافقة الإدارة.',
            'data' => $project
        ], 201);
    }

    /**
     * عرض تفاصيل مشروع معين
     */
    public function show($id)
    {
        $project = Project::query()
            ->with('organization.user:id,name,profile_image')
            ->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'المشروع غير موجود.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب تفاصيل المشروع بنجاح',
            'data' => $project
        ]);
    }

    /**
     * تعديل بيانات المشروع
     */
    public function update(Request $request, $id)
    {
        $project = Project::query()
            ->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'المشروع غير موجود.'
            ], 404);
        }

        // تحديث البيانات
        $project->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل بيانات المشروع بنجاح',
            'data' => $project
        ]);
    }

    /**
     * حذف أو أرشفة المشروع
     */
    public function destroy($id)
    {
        $project = Project::query()
            ->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'المشروع غير موجود.'
            ], 404);
        }

        $project->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المشروع بنجاح'
        ]);
    }
}
