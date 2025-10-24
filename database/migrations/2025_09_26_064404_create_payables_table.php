<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->integer('no_of_bags')->default(0);
            $table->decimal('amount_per_bag', 12, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('bilti_no')->nullable();
            $table->string('Truck_no')->nullable();
            $table->decimal('tons', 12, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payables');
    }
};
