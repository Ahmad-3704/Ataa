<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            // ربط المحفظة بالمستخدم (إذا تم حذف المستخدم تُحذف محفظته)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // حقل الرصيد (12 خانة، منها خانتين بعد الفاصلة للأرقام العشرية)
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
