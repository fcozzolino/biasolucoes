<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('cards', function (Blueprint $table) {
        $table->text('full_description')->nullable();
        $table->string('link')->nullable();
        $table->string('color', 20)->default('secondary'); // Ex: primary, success, warning...
    });
}

public function down()
{
    Schema::table('cards', function (Blueprint $table) {
        $table->dropColumn(['full_description', 'link', 'color']);
    });
}

};
