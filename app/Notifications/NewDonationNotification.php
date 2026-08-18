<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDonationNotification extends Notification
{
    use Queueable;

    public $donation;

    // نستقبل بيانات التبرع عند استدعاء الإشعار
    public function __construct($donation)
    {
        $this->donation = $donation;
    }

    // تحديد طريقة الإرسال (هنا سنحفظه في قاعدة البيانات فقط)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // شكل البيانات التي سيتم تخزينها في جدول الإشعارات
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تبرع جديد!',
            'message' => "تم استلام تبرع بقيمة {$this->donation->amount} للمشروع رقم {$this->donation->project_id}.",
            'donation_id' => $this->donation->id,
            'amount' => $this->donation->amount,
            'project_id' => $this->donation->project_id,
        ];
    }
}
