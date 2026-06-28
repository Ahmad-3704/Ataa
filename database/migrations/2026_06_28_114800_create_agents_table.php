<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            // ربط الجدول بالمستخدم
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // الحقول الخاصة بالمندوب
            $table->string('vehicle_type'); // نوع المركبة (مثال: شاحنة صغيرة، سيارة، دراجة نارية)
            $table->string('vehicle_number')->nullable(); // رقم اللوحة
            $table->boolean('is_available')->default(true); // حالة التفرغ (هل هو متاح لاستلام مهمة الآن؟)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
