<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAgentController extends Controller
{
    /**
     * جلب قائمة المندوبين وحالات طلباتهم
     */
    public function index()
    {
        // جلب جميع المستخدمين الذين يملكون صلاحية "مندوب"
        $agents = User::query()
            ->where('role', 'agent')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب قائمة المندوبين بنجاح',
            'data' => $agents
        ]);
    }

    /**
     * اعتماد أو رفض طلب توثيق المندوب
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        // البحث عن المندوب
        $agent = User::query()->where('role', 'agent')->find($id);

        if (!$agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'المندوب غير موجود.'
            ], 404);
        }

        // تحديث حالة الحساب
        // (ملاحظة: هذا يفترض أن لديك حقل 'status' في جدول users، إذا كان اسمه مختلفاً مثل 'account_status' يرجى تعديله هنا)
        $agent->status = $request->status;
        $agent->save();

        $statusMessage = $request->status === 'approved' ? 'اعتماد' : 'رفض';

        return response()->json([
            'status' => 'success',
            'message' => "تم {$statusMessage} المندوب بنجاح.",
            'data' => $agent
        ]);
    }
}
