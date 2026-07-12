<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الميجريشن لإنشاء الجدول.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // ربط المشروع بالجمعية التي أنشأته
            // استخدمنا organization_id لربطه بجدول organizations الذي أنشأناه سابقاً
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            // تفاصيل المشروع
            $table->string('title'); // اسم المشروع
            $table->text('description'); // وصف المشروع

            // الأمور المالية
            $table->decimal('target_amount', 10, 2); // المبلغ المستهدف
            $table->decimal('collected_amount', 10, 2)->default(0); // المبلغ المجمع (يبدأ من 0)

            // حالة المشروع (نشط، مكتمل، ملغى)
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * التراجع عن الميجريشن (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
