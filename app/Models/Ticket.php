<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'cliente_id',
        'user_id',
        'titulo',
        'descripcion',
        'estado',
        'ia_prioridad',
        'ia_categoria',
        'ia_resumen',
        'nota_tecnico',
        'evidencia',
        'latitud_capturada',
        'longitud_capturada',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
