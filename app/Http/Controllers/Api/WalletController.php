<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction; // استدعاء مودل العمليات

class WalletController extends Controller
{
    /**
     * شحن رصيد المحفظة (Deposit)
     */
    public function topUp(Request $request)
    {
        // 1. التحقق من صحة المبلغ المدخل
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();

        // 2. تحديث الرصيد في مودل المستخدم
        $user->wallet_balance += $request->amount;
        $user->save();

        // 3. إضافة الحركة لجدول العمليات (Transaction)
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'deposit', // نوع العملية: إيداع/شحن
            'status' => 'completed',

        ]);
        // إرسال إشعار للمستخدم
        Notification::create([
            'user_id' => $user->id,
            'title' => 'تم شحن المحفظة بنجاح',
            'message' => "تم إضافة مبلغ {$request->amount} إلى محفظتك. رصيدك الحالي هو {$user->wallet_balance}."
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم شحن المحفظة بنجاح.',
            'new_balance' => $user->wallet_balance
        ]);
    }
}
