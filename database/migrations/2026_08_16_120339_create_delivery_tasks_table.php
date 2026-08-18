<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_tasks', function (Blueprint $table) {
            $table->id();

            // ربط المهمة بالمشروع
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // ربط المهمة بالمندوب (والذي هو مستخدم موجود في جدول users)
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');

            // حالة المهمة
            $table->enum('status', ['pending', 'in_progress', 'delivered'])->default('pending');

            // رمز سري يتم توليده برمجياً لتأكيد التسليم (QR Code Token)
            $table->string('qr_code_token')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_tasks');
    }
};
