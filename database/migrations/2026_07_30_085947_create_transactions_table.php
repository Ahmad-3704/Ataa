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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id'); // رقم العملية
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // المستخدم
            $table->decimal('amount', 10, 2); // المبلغ
            $table->enum('type', ['deposit', 'donation', 'withdraw']); // نوع العملية
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed'); // حالة العملية
            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
