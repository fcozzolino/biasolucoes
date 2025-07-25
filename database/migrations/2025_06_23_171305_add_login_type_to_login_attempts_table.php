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
        Schema::table('login_attempts', function (Blueprint $table) {
    $table->string('login_type')->nullable()->after('user_agent');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('login_attempts', function (Blueprint $table) {
    $table->dropColumn('login_type');
});
    }
};
