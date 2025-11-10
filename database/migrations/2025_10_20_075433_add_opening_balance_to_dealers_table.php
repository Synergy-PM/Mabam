<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('dealers', function (Blueprint $table) {
        $table->decimal('opening_balance', 15, 2)->default(0)->after('city_id');
    });
}

public function down()
{
    Schema::table('dealers', function (Blueprint $table) {
        $table->dropColumn('opening_balance');
    });
}

};
