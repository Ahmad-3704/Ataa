<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectStatusNotification extends Notification
{
    use Queueable;

    public $project;
    public $statusMessage;

    public function __construct($project, $statusMessage)
    {
        $this->project = $project;
        $this->statusMessage = $statusMessage;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تحديث حالة المشروع',
            'message' => "تم {$this->statusMessage} مشروعك: {$this->project->title}",
            'project_id' => $this->project->id,
            'status' => $this->project->status,
        ];
    }
}
