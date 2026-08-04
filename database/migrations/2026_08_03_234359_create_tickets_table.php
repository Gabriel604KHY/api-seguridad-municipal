<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear la tabla de tickets municipales.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('titulo', 150);

            $table->text('descripcion');

            $table
                ->string('categoria', 50)
                ->default('general');

            $table
                ->string('prioridad', 20)
                ->default('media');

            $table
                ->string('estado', 20)
                ->default('abierto');

            $table
                ->string('ubicacion', 255)
                ->nullable();

            $table
                ->timestamp('cerrado_at')
                ->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('categoria');
            $table->index(['estado', 'prioridad']);
        });
    }

    /**
     * Eliminar la tabla de tickets.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};