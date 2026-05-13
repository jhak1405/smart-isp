<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        $isTecnico = auth()->user()->role === 'Técnico';

        return $schema->components([

            // ── Sección 1: Asignación ──────────────────────────────────
            Section::make('Asignación')
                ->icon('heroicon-o-user-group')
                ->columns(2)
                ->schema([
                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->relationship('cliente', 'nombre_completo')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled($isTecnico)
                        ->columnSpanFull(),

                    // Info de dirección/teléfono del cliente seleccionado
                    Placeholder::make('cliente_info')
                        ->label('Referencia del Cliente')
                        ->hidden(fn (string $operation): bool => $operation === 'create')
                        ->content(function ($record) {
                            if (!$record || !$record->cliente) return '—';
                            $dir = $record->cliente->direccion_escrita ?: 'Sin dirección';
                            $tel = $record->cliente->telefono ?: 'Sin teléfono';
                            return new \Illuminate\Support\HtmlString(
                                '<div style="font-size:0.85rem;line-height:1.7;">' .
                                '<strong>📍 Dirección:</strong> ' . e($dir) . '<br>' .
                                '<strong>📞 Teléfono:</strong> ' . e($tel) .
                                '</div>'
                            );
                        })
                        ->columnSpanFull(),

                    Select::make('user_id')
                        ->label('Técnico Asignado')
                        ->relationship('tecnico', 'name', fn (Builder $query) => $query->where('role', 'Técnico'))
                        ->nullable()
                        ->searchable()
                        ->preload()
                        ->disabled($isTecnico)
                        ->columnSpanFull(),
                ]),

            // ── Sección 2: Detalle del Ticket ─────────────────────────
            Section::make('Detalle del Ticket')
                ->icon('heroicon-o-document-text')
                ->columns(2)
                ->schema([
                    TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->minLength(5)
                        ->maxLength(255)
                        ->disabled($isTecnico)
                        ->columnSpanFull(),

                    Textarea::make('descripcion')
                        ->label('Descripción del Problema')
                        ->required()
                        ->minLength(10)
                        ->rows(4)
                        ->disabled($isTecnico)
                        ->columnSpanFull(),
                ]),

            // ── Sección 3: Gestión Operativa ──────────────────────────
            Section::make('Gestión Operativa')
                ->icon('heroicon-o-cog-6-tooth')
                ->columns(2)
                ->schema([
                    Select::make('estado')
                        ->label('Estado del Ticket')
                        ->required()
                        ->options([
                            'Abierto'    => 'Abierto',
                            'En Proceso' => 'En Proceso',
                            'Resuelto'   => 'Resuelto',
                            'Cerrado'    => 'Cerrado',
                        ])
                        ->default('Abierto')
                        ->native(false),

                    Textarea::make('notas_equipamiento')
                        ->label('⚠️ Notas de Equipamiento')
                        ->helperText('Indica qué materiales debe llevar el técnico.')
                        ->rows(2)
                        ->placeholder('Ej: Cable RJ45 cat6, router TP-Link, escalera...')
                        ->disabled($isTecnico)
                        ->columnSpanFull(),
                ]),

        ]);
    }
}
