<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    /**
     * جلب الإحصائيات العامة للوحة تحكم الإدارة
     */
    public function index()
    {
        // 1. إجمالي التبرعات (مجموع كل المبالغ في جدول التبرعات)
        $totalDonations = Donation::sum('amount');

        // 2. الحالات النشطة (المشاريع المقبولة التي تعمل حالياً)
        $activeProjects = Project::query()->where('status', 'approved')->count();

        // 3. الحالات المكتملة (المشاريع التي وصل مبلغها المجمع لهدفها)
       $completedProjects = Project::whereColumn('collected_amount', '>=', 'target_amount', 'and')->count();
        // 4. عدد المندوبين النشطين (المعتمدين)
        // ملاحظة: استخدمنا 'approved' كحالة بناءً على الكنترولر السابق
        $activeAgents = User::query()->where('role', 'agent')->where('status', 'approved')->count();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الإحصائيات بنجاح',
            'data' => [
                'total_donations' => $totalDonations,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'active_agents' => $activeAgents,
            ]
        ]);
    }
}
