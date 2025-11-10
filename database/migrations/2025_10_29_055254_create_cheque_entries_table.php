<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cheque_entries', function (Blueprint $table) {
    $table->id();
    $table->date('date');

    $table->enum('party_type', ['supplier', 'dealer', 'expense']);
    $table->unsignedBigInteger('party_id')->nullable();
    $table->string('expense_description')->nullable();
    $table->decimal('credit', 12, 2)->default(0);
    $table->decimal('debit', 12, 2)->default(0);
    $table->enum('payment_type', ['cash', 'online', 'cheque'])->nullable();

    $table->timestamps();
    $table->softDeletes();

    $table->index(['party_id', 'party_type']);
});

    }

    public function down()
    {
        Schema::table('cheque_entries', function (Blueprint $table) {
            $table->dropForeign(['party_id']);
        });

        Schema::dropIfExists('cheque_entries');
    }
};
