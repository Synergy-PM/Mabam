<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('payable_id')->constrained('payables')->onDelete('cascade');
            $table->string('bilti_no')->nullable();
            $table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
            $table->integer('bags')->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('freight', 12, 2)->default(0);
            $table->decimal('tons', 12, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->enum('payment_type', ['cash', 'credit','online','cheque'])->default('cash')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('receivables');
    }
};
