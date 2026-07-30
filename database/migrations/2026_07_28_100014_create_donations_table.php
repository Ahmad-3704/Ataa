<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // المتبرع (ربط مع جدول المستخدمين)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // المشروع المُتبرع له (ربط مع جدول المشاريع)
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // مبلغ التبرع
            $table->decimal('amount', 10, 2);

            // حالة التبرع (مكتمل بشكل افتراضي)
            $table->string('status')->default('completed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
