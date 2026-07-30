<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // تحديد المفتاح الأساسي لأننا غيرنا اسمه
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
    ];

    // العملية تابعة لمستخدم واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
