<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Project;
use App\Models\Transaction; // تم إضافة مودل العمليات هنا
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * استعراض سجل تبرعات المستخدم
     */
    public function index(Request $request)
    {
        // استخدام الأسلوب المعتمد: تهيئة الاستعلام أولاً
        $donations = Donation::query()
            ->where('user_id', $request->user()->id)
            ->with('project:id,title') // جلب اسم المشروع فقط لتخفيف الضغط
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب سجل التبرعات بنجاح',
            'data' => $donations
        ]);
    }

    /**
     * عملية تبرع لمشروع
     */
    public function store(Request $request)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $amount = $request->amount;

        // 1. التحقق من أن الرصيد يكفي
        if ($user->wallet_balance < $amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'رصيدك غير كافٍ لإتمام التبرع.'
            ], 400);
        }

        // 2. خصم المبلغ من محفظة المتبرع
        $user->wallet_balance -= $amount;
        $user->save();

        // 3. زيادة المبلغ المجمع في المشروع
        $project = Project::query()->find($request->project_id);
        $project->collected_amount += $amount;
        $project->save();

        // 4. تسجيل التبرع في جدول التبرعات الخاص بالفرونت إند
        $donation = Donation::query()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => $amount,
        ]);

        // 5. توثيق العملية في جدول العمليات المركزي
        Transaction::query()->create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'donation', // نوع العملية: تبرع
            'status' => 'completed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم التبرع بنجاح، جزاك الله خيراً!',
            'donation' => $donation,
            'remaining_balance' => $user->wallet_balance
        ]);
    }
}
