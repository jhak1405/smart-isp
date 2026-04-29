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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('dni_ruc')->unique()->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion_escrita')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();   // ej: -12.04318000
            $table->decimal('longitud', 11, 8)->nullable();  // ej: -77.02824100
            $table->string('estado')->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
