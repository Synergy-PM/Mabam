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
        Schema::table('receivables', function (Blueprint $table) {
    $table->unsignedBigInteger('receivable_payment_id')->nullable()->after('payable_id');
    $table->foreign('receivable_payment_id')
          ->references('id')
          ->on('receivable_payments')
          ->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            //
        });
    }
};
