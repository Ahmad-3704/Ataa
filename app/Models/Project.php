<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح تعبئتها
     */
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'target_amount',
        'collected_amount',
        'is_urgent', // تمت إضافة الحقل هنا
        'status',
    ];

    /**
     * تحويل أنواع الحقول (Casting)
     */
    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'is_urgent' => 'boolean', // التأكد من أن القيمة تُعامل كـ Boolean
        ];
    }

    /**
     * المشروع يتبع لجمعية واحدة
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * المشروع قد يحتوي على عدة تبرعات
     */
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
