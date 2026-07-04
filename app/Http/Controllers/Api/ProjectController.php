<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * عرض جميع المشاريع المتاحة
     */
    public function index()
    {
        // جلب كل المشاريع النشطة مع بيانات الجمعية (واسم المستخدم الخاص بالجمعية)
        $projects = Project::with('organization.user')
            ->where('status', 'active')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المشاريع بنجاح',
            'data' => $projects
        ]);
    }

    /**
     * إضافة مشروع جديد (للجمعيات فقط)
     */
    public function store(Request $request)
    {
        // 1. جلب المستخدم الحالي (الجمعية)
         $user = $request->user();
        // 2. التأكد من أن الجمعية أكملت ملفها الشخصي (البروفايل)
        $organization = $user->organization;
        if (!$organization) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب إكمال بيانات الجمعية (البروفايل) أولاً قبل إضافة مشاريع.'
            ], 400);
        }

        // 3. التحقق من صحة البيانات المرسلة
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:10', // أقل مبلغ للمشروع مثلاً 10
        ]);

        // 4. إنشاء المشروع وربطه بالجمعية
        $project = Project::create([
            'organization_id' => $organization->id,
            'title' => $request->title,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'status' => 'active', // المشروع يبدأ كنشط
        ]);

        // 5. إرسال استجابة النجاح
        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة المشروع الخيري بنجاح!',
            'data' => $project
        ], 201);
    }
}
