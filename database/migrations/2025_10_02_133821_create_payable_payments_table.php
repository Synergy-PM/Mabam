<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payable_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')->constrained()->onDelete('cascade'); 
            $table->date('transaction_date');
            $table->enum('transaction_type', ['debit', 'credit']); // debit = payment given, credit = refund/adjustment
            $table->decimal('amount', 15, 2);
            $table->string('payment_mode')->nullable(); // cash, bank, cheque, online
            $table->string('proof_of_payment')->nullable();
            $table->text('notes')->nullable();

            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payable_payments');
    }
};
