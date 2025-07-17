<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_standalone')->default(false);
            $table->boolean('requires_workspace')->default(true);
            $table->string('icon', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('modules')->insert([
            [
                'name' => 'Tarefas',
                'slug' => 'tarefas',
                'description' => 'Gerencie suas tarefas com quadros Kanban',
                'is_standalone' => true,
                'requires_workspace' => false,
                'icon' => 'fas fa-tasks',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Financeiro',
                'slug' => 'financeiro',
                'description' => 'Controle completo das finanças',
                'is_standalone' => false,
                'requires_workspace' => true,
                'icon' => 'fas fa-dollar-sign',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RH',
                'slug' => 'rh',
                'description' => 'Gestão de recursos humanos',
                'is_standalone' => false,
                'requires_workspace' => true,
                'icon' => 'fas fa-users',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CRM',
                'slug' => 'crm',
                'description' => 'Relacionamento com clientes',
                'is_standalone' => false,
                'requires_workspace' => true,
                'icon' => 'fas fa-handshake',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('modules');
    }
};
