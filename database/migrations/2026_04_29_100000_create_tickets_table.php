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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');
            
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('estado')->default('Abierto');
            
            // Campos para Sprint 2 (Motor de IA)
            $table->string('ia_prioridad')->nullable();
            $table->string('ia_categoria')->nullable();
            $table->text('ia_resumen')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
