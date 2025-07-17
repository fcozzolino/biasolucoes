<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('workspace_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('type', 50)->after('workspace_id');
            $table->text('description')->after('type');
            $table->string('ip_address', 45)->nullable()->after('description');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->json('properties')->nullable()->after('user_agent');
            $table->string('causer_type')->nullable()->after('properties');
            $table->unsignedBigInteger('causer_id')->nullable()->after('causer_type');

            // Indexes
            $table->index('user_id');
            $table->index('workspace_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['causer_type', 'causer_id']);
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['workspace_id']);
            $table->dropColumn([
                'user_id',
                'workspace_id',
                'type',
                'description',
                'ip_address',
                'user_agent',
                'properties',
                'causer_type',
                'causer_id'
            ]);
        });
    }
};
