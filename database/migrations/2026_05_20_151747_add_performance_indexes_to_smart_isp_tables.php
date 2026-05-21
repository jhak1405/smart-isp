<?php

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
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('estado');
            $table->index('ia_prioridad');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index('dni_ruc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['ia_prioridad']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['dni_ruc']);
        });
    }
};
