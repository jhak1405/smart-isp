<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\PendienteObserver;

#[ObservedBy([PendienteObserver::class])]
class Pendiente extends Model
{
    protected $fillable = [
        'cliente_id',
        'tipo',
        'descripcion',
        'fecha_recordatorio',
        'estado',
        'user_id',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_recordatorio' => 'date',
        ];
    }

    /** Cliente del pendiente */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /** Técnico asignado */
    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Usuario que creó el pendiente */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /** Scope: solo pendientes activos (no completados) */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'Pendiente');
    }

    /** ¿Está vencido? (fecha pasada y aún pendiente) */
    public function getIsVencidoAttribute(): bool
    {
        return $this->estado === 'Pendiente'
            && $this->fecha_recordatorio->isPast();
    }

    /** ¿Vence hoy? */
    public function getIsHoyAttribute(): bool
    {
        return $this->fecha_recordatorio->isToday();
    }
}
