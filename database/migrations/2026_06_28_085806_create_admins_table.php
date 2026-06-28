<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المدير
            $table->string('email')->unique(); // البريد الإلكتروني للمدير
            $table->string('password'); // كلمة المرور
            $table->boolean('is_super_admin')->default(false); // حقل يميز إذا كان مدير عام أو مدير بصلاحيات محدودة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
