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
        Schema::table('pendientes', function (Blueprint $table) {
            // Eliminar campos innecesarios
            $table->dropColumn('titulo');
            $table->dropColumn('direccion');
            $table->dropColumn('costo');

            // Convertir tipo a un string libre en lugar de enum (o si tu db no soporta modificar enum fácilmente, creamos uno nuevo)
            // Primero dropeamos la columna vieja y la recreamos
            $table->dropColumn('tipo');
        });

        Schema::table('pendientes', function (Blueprint $table) {
            // Añadir el tipo como texto
            $table->string('tipo')->nullable()->after('id');
            // Relación con el cliente
            $table->foreignId('cliente_id')
                  ->nullable()
                  ->after('tipo')
                  ->constrained('clientes')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendientes', function (Blueprint $table) {
            $table->string('titulo');
            $table->string('direccion')->nullable();
            $table->decimal('costo', 8, 2)->nullable();
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
            $table->dropColumn('tipo');
        });

        Schema::table('pendientes', function (Blueprint $table) {
            $table->enum('tipo', [
                'Instalación',
                'Cobro',
                'Retiro de Equipo',
                'Mantenimiento',
                'Otro',
            ])->default('Otro')->after('titulo');
        });
    }
};
