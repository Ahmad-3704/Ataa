<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // الحقول المسموح إدخالها (الحماية من الإدخال العشوائي)
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'target_amount',
        'collected_amount',
        'status',
    ];

    /**
     * علاقة المشروع مع الجمعية:
     * المشروع الواحد "ينتمي إلى" جمعية واحدة
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
