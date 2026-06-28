<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ضروري جداً لتوليد توكن تسجيل الدخول

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * الحقول المسموح تعبئتها
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'profile_image',
    ];

    /**
     * الحقول المخفية (لا تظهر عند استرجاع البيانات)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع الحقول
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // المستخدم يملك محفظة واحدة
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    // المستخدم قد يكون لديه ملف جمعية ملحق
    public function organization()
    {
        return $this->hasOne(Organization::class);
    }
    // المستخدم قد يكون لديه ملف مندوب ملحق
    public function agent()
    {
        return $this->hasOne(Agent::class);
    }
    // المستخدم قد يكون مستفيداً
    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class);
    }

    // المستخدم قد يكون متبرعاً
    public function donor()
    {
        return $this->hasOne(Donor::class);
    }
}
