<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        $isTecnico = auth()->user()->role === 'Técnico';

        return $schema
            ->columns(2)
            ->components([

                // ── Fila 1: Cliente (col completa) ────────────────────────
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled($isTecnico)
                    ->columnSpanFull(),

                // ── Referencia del cliente (solo en edición) ──────────────
                Placeholder::make('cliente_info')
                    ->label('Referencia')
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->content(function ($record) {
                        if (!$record || !$record->cliente) return '—';
                        $dir = $record->cliente->direccion_escrita ?: 'Sin dirección registrada';
                        $tel = $record->cliente->telefono       ?: 'Sin teléfono registrado';
                        return new \Illuminate\Support\HtmlString(
                            '<span style="font-size:0.82rem;color:#6b7280;line-height:1.8;">' .
                            '<strong>Dirección:</strong> ' . e($dir) . '&nbsp;&nbsp;|&nbsp;&nbsp;' .
                            '<strong>Teléfono:</strong> '  . e($tel) .
                            '</span>'
                        );
                    })
                    ->columnSpanFull(),

                // ── Fila 2: Técnico | Estado ───────────────────────────────
                Select::make('user_id')
                    ->label('Técnico Asignado')
                    ->relationship(
                        'tecnico',
                        'name',
                        fn (Builder $query) => $query->where('role', 'Técnico')
                    )
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->disabled($isTecnico)
                    ->placeholder('Sin asignar'),

                Select::make('estado')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'Abierto'    => 'Abierto',
                        'En Proceso' => 'En Proceso',
                        'Resuelto'   => 'Resuelto',
                        'Cerrado'    => 'Cerrado',
                    ])
                    ->default('Abierto')
                    ->native(false),

                // ── Fila 3: Título (col completa) ─────────────────────────
                TextInput::make('titulo')
                    ->label('Título del Ticket')
                    ->required()
                    ->minLength(5)
                    ->maxLength(255)
                    ->disabled($isTecnico)
                    ->columnSpanFull(),

                // ── Fila 4: Descripción (col completa) ────────────────────
                Textarea::make('descripcion')
                    ->label('Descripción del Problema')
                    ->required()
                    ->minLength(10)
                    ->rows(4)
                    ->disabled($isTecnico)
                    ->columnSpanFull(),

                // ── Fila 5: Notas de Equipamiento (col completa) ──────────
                Textarea::make('notas_equipamiento')
                    ->label('Notas de Equipamiento')
                    ->helperText('Materiales o herramientas que debe llevar el técnico.')
                    ->rows(2)
                    ->placeholder('Ej: Cable RJ45 cat6, router TP-Link, escalera...')
                    ->disabled($isTecnico)
                    ->columnSpanFull(),

            ]);
    }
}
