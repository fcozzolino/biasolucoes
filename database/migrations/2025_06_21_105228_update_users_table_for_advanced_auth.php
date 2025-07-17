<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename company_id to workspace_id
            $table->renameColumn('company_id', 'workspace_id');

            // Add new columns
            $table->string('phone', 20)->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('preferred_language', 5)->default('pt-BR');
            $table->string('timezone', 50)->default('America/Sao_Paulo');
            $table->enum('theme', ['light', 'dark', 'auto'])->default('auto');
            $table->json('preferences')->nullable();
            $table->boolean('is_active')->default(true);

            // Indexes
            $table->index('phone');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('workspace_id', 'company_id');
            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'last_login_at',
                'last_login_ip',
                'preferred_language',
                'timezone',
                'theme',
                'preferences',
                'is_active'
            ]);
        });
    }
};
