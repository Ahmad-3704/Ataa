<?php

namespace App\Console\Commands;
use App\Models\Notification;
use Illuminate\Console\Command;
use App\Models\AutoDonation;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class ProcessAutoDonations extends Command
{
    /**
     * اسم الأمر الذي سيُنفذ في الـ Terminal
     */
    protected $signature = 'donations:process-auto';

    /**
     * وصف الأمر
     */
    protected $description = 'معالجة التبرعات التلقائية (اليومية، الأسبوعية، الشهرية)';

    /**
     * تنفيذ الأمر
     */
    public function handle()
    {
        $this->info('بدأ معالجة التبرعات التلقائية...');

        // جلب جميع التبرعات التلقائية النشطة
        $autoDonations = AutoDonation::query()->where('status', 'active')->get();

        $count = 0; // لحساب كم عملية تمت

        foreach ($autoDonations as $donationConfig) {
         $user = User::query()->find($donationConfig->user_id);
            if (!$user) continue;

            // التحقق من أن موعد التبرع قد حان بناءً على الفترة الزمنية (interval)
            // ولتبسيط الأمر سنعتمد على متى تم آخر تعديل (أو يمكن إضافة حقل last_processed_at لاحقاً)
            $lastProcessed = $donationConfig->updated_at; // نستخدم updated_at مؤقتاً كمرجع
            $now = Carbon::now();
            $shouldProcess = false;

            switch ($donationConfig->interval) {
                case 'daily':
                    $shouldProcess = $lastProcessed->diffInDays($now) >= 1;
                    break;
                case 'weekly':
                    $shouldProcess = $lastProcessed->diffInWeeks($now) >= 1;
                    break;
                case 'monthly':
                    $shouldProcess = $lastProcessed->diffInMonths($now) >= 1;
                    break;
            }

            // لتجاوز الفحص لأغراض الاختبار وتجربة الخصم مباشرة، قم بإزالة الشرط $shouldProcess في بيئة التطوير
            // if ($shouldProcess) {

                // 1. التحقق من توفر الرصيد
                if ($user->wallet_balance >= $donationConfig->amount) {

                    // 2. خصم الرصيد
                    $user->wallet_balance -= $donationConfig->amount;
                    $user->save();

                    // 3. تسجيل العملية كـ "تبرع تلقائي" في جدول العمليات
                    Transaction::create([
                        'user_id' => $user->id,
                        'amount' => $donationConfig->amount,
                        'type' => 'donation', // يمكن تغييرها لاحقاً إلى auto_donation إذا تم إضافتها للـ enum
                        'status' => 'completed',
                    ]);
// إشعار بالنجاح
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => 'تم تنفيذ التبرع التلقائي',
                        'message' => "تم خصم مبلغ {$donationConfig->amount} كتبرع تلقائي من محفظتك. تقبل الله!"
                    ]);
                    // (هنا يمكننا لاحقاً إضافة الكود الخاص بإرسال الإشعار بنجاح العملية)

                    // 4. تحديث وقت آخر معالجة
                    $donationConfig->touch(); // لتحديث حقل updated_at

                    $count++;
                    $this->info("تم خصم {$donationConfig->amount} بنجاح من المستخدم: {$user->name}");
                } else {
                    // (هنا يمكننا لاحقاً إضافة الكود الخاص بإرسال إشعار بنقص الرصيد)
                    $this->error("الرصيد غير كافٍ للمستخدم: {$user->name}");
                    // 1. التحقق من توفر الرصيد
                if ($user->wallet_balance >= $donationConfig->amount) {

                    // ... (كود الخصم وإشعار النجاح اللي ضفناه) ...

                } else {
                    $this->error("الرصيد غير كافٍ للمستخدم: {$user->name}");

                    // إشعار بالفشل
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => 'فشل التبرع التلقائي',
                        'message' => "لم يتم تنفيذ تبرعك التلقائي بمبلغ {$donationConfig->amount} بسبب عدم كفاية الرصيد. يرجى شحن محفظتك."
                    ]);
                }
                }
            // }
        }

        $this->info("انتهت المعالجة. إجمالي العمليات الناجحة: {$count}");
    }
}
