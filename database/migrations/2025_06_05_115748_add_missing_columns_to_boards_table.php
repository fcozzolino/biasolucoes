<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToBoardsTable extends Migration // <- Corrigir aqui (remover o "o" extra)
{
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            if (!Schema::hasColumn('boards', 'color')) {
                $table->string('color', 7)->default('#6366F1')->after('title');
            }

            if (!Schema::hasColumn('boards', 'status')) {
                $table->tinyInteger('status')->default(0)->after('user_id');
            }

            if (!Schema::hasColumn('boards', 'last_viewed_at')) {
                $table->timestamp('last_viewed_at')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn(['color', 'status', 'last_viewed_at']);
        });
    }
}
