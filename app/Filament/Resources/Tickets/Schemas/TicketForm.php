<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('user_id')
                    ->label('Técnico Asignado')
                    ->relationship('tecnico', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->minLength(5)
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->minLength(10)
                    ->columnSpanFull(),

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

                // -------------------------------------------------------
                // Clasificación de IA (Solo lectura)
                // -------------------------------------------------------
                Section::make('Clasificación de Inteligencia Artificial')
                    ->description('Estos campos son generados automáticamente por el motor de IA (Gemini). No se pueden editar manualmente.')
                    ->icon('heroicon-o-sparkles')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('ia_prioridad')
                            ->label('Prioridad (IA)')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Pendiente de análisis'),

                        TextInput::make('ia_categoria')
                            ->label('Categoría (IA)')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Pendiente de análisis'),

                        Textarea::make('ia_resumen')
                            ->label('Resumen (IA)')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3)
                            ->placeholder('El resumen generado por la IA aparecerá aquí.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
