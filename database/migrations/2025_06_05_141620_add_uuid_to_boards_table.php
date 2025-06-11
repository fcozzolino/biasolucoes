<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddUuidToBoardsTable extends Migration
{
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            if (!Schema::hasColumn('boards', 'uuid')) {
                $table->uuid('uuid')->unique()->after('id')->nullable();
            }
        });

        // Gerar UUIDs para boards existentes
        \App\Models\Board::whereNull('uuid')->each(function ($board) {
            $board->update(['uuid' => Str::uuid()]);
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
