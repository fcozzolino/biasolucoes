<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  public function up()
  {
    Schema::rename('companies', 'workspaces');

    Schema::table('workspaces', function (Blueprint $table) {
      $table->string('logo')->nullable()->after('phone');
      $table->json('settings')->nullable()->after('plan_id');
      $table->string('subdomain', 50)->nullable()->unique()->after('slug');
    });
  }

  public function down()
  {
    Schema::table('workspaces', function (Blueprint $table) {
      $table->dropColumn(['logo', 'settings', 'subdomain']);
    });

    Schema::rename('workspaces', 'companies');
  }
};
