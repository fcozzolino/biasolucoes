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
    Schema::table('login_attempts', function (Blueprint $table) {
        
        $table->string('provider')->nullable()->after('login_type');
        $table->boolean('is_successful')->default(false)->after('attempted_at');
        $table->json('device_info')->nullable()->after('failure_reason');
        $table->text('location')->nullable()->after('device_info');
    });
}

public function down()
{
    Schema::table('login_attempts', function (Blueprint $table) {
        $table->dropColumn([
            'login_type', 'provider', 'is_successful', 'device_info', 'location'
        ]);
    });
}

};
