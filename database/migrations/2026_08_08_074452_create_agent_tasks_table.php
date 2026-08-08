<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete(); // المندوب الذي سينفذ المهمة
            $table->foreignId('donation_id')->nullable()->constrained()->cascadeOnDelete(); // التبرع المرتبط (إن وجد)
            $table->string('title'); // عنوان المهمة (مثال: استلام تبرع ملابس)
            $table->text('description')->nullable(); // تفاصيل إضافية للمندوب
            $table->string('qr_code')->unique(); // الكود العشوائي للـ QR
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending'); // حالة المهمة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_tasks');
    }
};
