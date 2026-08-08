<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoDonation extends Model
{
    use HasFactory;

    // تحديد المفتاح الأساسي بما أننا غيرنا اسمه الافتراضي
    protected $primaryKey = 'auto_donation_id';

    // الحقول المسموح تعبئتها
    protected $fillable = [
        'user_id',
        'amount',
        'interval',
        'status',
    ];

    /**
     * التبرع التلقائي يعود لمستخدم واحد
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
