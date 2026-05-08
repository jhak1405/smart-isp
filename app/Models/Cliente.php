<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre_completo',
        'dni_ruc',
        'telefono',
        'direccion_escrita',
        'foto_fachada',
        'latitud',
        'longitud',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'latitud'  => 'decimal:8',
            'longitud' => 'decimal:8',
        ];
    }
}
