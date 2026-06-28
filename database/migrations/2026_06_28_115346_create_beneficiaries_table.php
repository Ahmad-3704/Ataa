<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // حقول إضافية قد تهم النظام الخيري للمستفيد
            $table->string('national_id')->unique()->nullable(); // الرقم الوطني أو الهوية
            $table->string('address')->nullable(); // السكن الحالي
            $table->integer('family_members_count')->default(1); // عدد أفراد الأسرة

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
