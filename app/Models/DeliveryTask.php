<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DeliveryTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'agent_id',
        'status',
        'qr_code_token',
    ];

    // دالة تعمل تلقائياً قبل حفظ البيانات في القاعدة
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($task) {
            // توليد رمز عشوائي فريد مكون من 40 حرف ليكون كود الـ QR
            $task->qr_code_token = Str::random(40);
        });
    }

    // علاقة المهمة بالمشروع
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // علاقة المهمة بالمندوب
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
