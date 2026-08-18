<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTaskNotification extends Notification
{
    use Queueable;

    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'مهمة توصيل جديدة!',
            'message' => "تم تكليفك بمهمة توصيل جديدة للمشروع رقم {$this->task->project_id}.",
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
        ];
    }
}
