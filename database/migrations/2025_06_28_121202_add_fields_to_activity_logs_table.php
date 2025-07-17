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
    Schema::table('activity_logs', function (Blueprint $table) {
        $table->string('session_id')->nullable()->after('user_agent');
        $table->string('url')->nullable()->after('session_id');
        $table->string('method', 10)->nullable()->after('url');
        $table->string('status', 20)->nullable()->after('method');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('activity_logs', function (Blueprint $table) {
        $table->dropColumn(['session_id', 'url', 'method', 'status']);
    });
}
};
