<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('address');
            $table->string('contact_no')->nullable()->after('contact_person');
            $table->string('contact_email')->nullable()->after('contact_no');
        });
    }

    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'contact_no', 'contact_email']);
        });
    }
};
