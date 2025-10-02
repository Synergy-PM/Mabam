<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payable_id')->nullable()->constrained()->onDelete('cascade');

            $table->foreignId('dealer_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('bilti_no')->nullable(); // <--- add this line
            $table->string('code')->nullable()->unique();
            $table->integer('bags')->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('freight', 10, 2)->default(0)->nullable();
            $table->decimal('tons', 10, 3)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('payment_type', ['credit','cash', 'online', 'cheque']);
            $table->string('proof_of_payment')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('receivables');
    }
};
