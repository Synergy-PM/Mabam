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
        Schema::create('dealers', function (Blueprint $table) {
        $table->id();
        $table->string('dealer_name')->nullable();
        $table->string('company_name')->nullable();
        $table->unsignedBigInteger('city_id')->nullable();
        $table->string('email')->nullable();
        $table->string('whatsapp')->nullable();
        $table->text('address')->nullable();
        $table->enum('transaction_type', ['debit', 'credit'])->default('debit');
        $table->timestamps();
        $table->softDeletes();
        $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
});

    }
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
        });

        Schema::dropIfExists('dealers');
    }
};

