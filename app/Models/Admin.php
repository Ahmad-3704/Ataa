<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // مهم جداً: نستخدم Authenticatable بدل Model
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ضروري لإنشاء التوكن

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_super_admin' => 'boolean', // تحويل القيمة لـ true/false
        ];
    }
}
