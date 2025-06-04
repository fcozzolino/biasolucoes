<?php
// database/migrations/2024_12_27_000001_create_labels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabela de etiquetas
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7); // Hex color #FFFFFF
            $table->unsignedBigInteger('board_id')->nullable(); // Se quiser etiquetas por board
            $table->unsignedBigInteger('user_id')->nullable(); // Se quiser etiquetas por usuário
            $table->timestamps();

            // Índices
            $table->index('board_id');
            $table->index('user_id');

            // Foreign keys (ajuste conforme suas tabelas)
            // $table->foreign('board_id')->references('id')->on('boards')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Tabela pivot para relacionamento many-to-many entre cards e labels
        Schema::create('card_label', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('label_id');
            $table->timestamps();

            // Índices únicos para evitar duplicatas
            $table->unique(['card_id', 'label_id']);

            // Foreign keys
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('label_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_label');
        Schema::dropIfExists('labels');
    }
};
