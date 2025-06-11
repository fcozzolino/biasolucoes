<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorAndStatusToBoardsTable extends Migration
{
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->string('color', 7)->default('#6366F1')->after('title'); // Primary color
            $table->tinyInteger('status')->default(0)->after('user_id'); // 0=ativo, 1=arquivado, 5=excluido
            $table->timestamp('last_viewed_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn(['color', 'status', 'last_viewed_at']);
        });
    }
}
