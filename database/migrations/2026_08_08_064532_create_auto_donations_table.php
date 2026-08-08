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
        Schema::create('auto_donations', function (Blueprint $table) {
            $table->id('auto_donation_id'); // رقم العملية
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // المستخدم (المتبرع)
            $table->decimal('amount', 10, 2); // المبلغ المراد التبرع به تلقائياً
            $table->enum('interval', ['daily', 'weekly', 'monthly']); // الفترة (يومي، أسبوعي، شهري)
            $table->enum('status', ['active', 'inactive'])->default('active'); // حالة التبرع التلقائي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_donations');
    }
};
