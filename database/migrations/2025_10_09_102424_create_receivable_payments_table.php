<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receivable_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->date('transaction_date')->nullable();
            $table->decimal('amount_received', 12, 2)->default(0);
            $table->string('payment_mode')->nullable();
            $table->string('transaction_type')->nullable(); 
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivable_payments');
    }
};
