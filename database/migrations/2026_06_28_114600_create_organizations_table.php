<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            // ربط الجدول بجدول المستخدمين (علاقة رأس برأس)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // الحقول الخاصة بالجمعية الخيرية
            $table->string('license_number')->unique(); // رقم الترخيص الرسمي
            $table->string('address'); // المقر أو العنوان الرئيسي
            $table->text('description')->nullable(); // نبذة عن الجمعية وأهدافها

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
