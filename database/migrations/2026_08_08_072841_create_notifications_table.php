<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // صاحب الإشعار
            $table->string('title'); // عنوان الإشعار
            $table->text('message'); // تفاصيل الإشعار
            $table->boolean('is_read')->default(false); // حالة القراءة (مقروء/غير مقروء)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
