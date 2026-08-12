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
            $table->foreignId('organization_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('target_amount', 10, 2); // المبلغ المطلوب
            $table->decimal('collected_amount', 10, 2)->default(0); // المبلغ المجمع
            $table->boolean('is_urgent')->default(false); // حالة عاجلة
            // ركز هنا: ضفنا كل الحالات الممكنة
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
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
