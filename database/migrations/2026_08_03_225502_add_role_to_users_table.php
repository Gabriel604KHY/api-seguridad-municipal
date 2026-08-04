<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar el rol a la tabla de usuarios.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table
                ->string('role', 20)
                ->default('operador')
                ->after('password')
                ->index();
        });
    }

    /**
     * Eliminar el rol si se revierte la migración.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};