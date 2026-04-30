<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        $isTecnico = auth()->user()->role === 'Técnico';

        return $schema
            ->components([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled($isTecnico),

                \Filament\Forms\Components\Placeholder::make('cliente_info')
                    ->label('Datos del Cliente')
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->content(function ($record) {
                        if (!$record || !$record->cliente) return '—';
                        return 'Dirección: ' . ($record->cliente->direccion_escrita ?: 'No registrada') . 
                               ' | Teléfono: ' . ($record->cliente->telefono ?: 'No registrado');
                    })
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->label('Técnico Asignado')
                    ->relationship('tecnico', 'name', fn(Builder $query) => $query->where('role', 'Técnico'))
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->disabled($isTecnico),

                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->minLength(5)
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->disabled($isTecnico),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->minLength(10)
                    ->columnSpanFull()
                    ->disabled($isTecnico),

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
                    ->hidden(fn (string $operation): bool => $operation === 'create')
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

                // -------------------------------------------------------
                // Resolución de Campo (Para el Técnico)
                // -------------------------------------------------------
                Section::make('Resolución en Campo')
                    ->description('Evidencia y notas del trabajo realizado.')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->schema([
                        Textarea::make('nota_tecnico')
                            ->label('Nota del Técnico')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        FileUpload::make('evidencia')
                            ->label('Evidencia Fotográfica')
                            ->image()
                            ->directory('evidencias-tickets')
                            ->columnSpanFull(),

                        \Dotswan\MapPicker\Fields\Map::make('ubicacion_gps')
                            ->label('Ubicación del Técnico (GPS)')
                            ->columnSpanFull()
                            ->defaultLocation(latitude: -5.19449, longitude: -80.63282) // Centro de Piura
                            ->showMarker()
                            ->showFullscreenControl()
                            ->showZoomControl()
                            ->showMyLocationButton()
                            ->draggable()
                            ->clickable(true)
                            ->afterStateUpdated(function ($set, ?array $state): void {
                                if (isset($state['lat']) && isset($state['lng'])) {
                                    // Forzar formato con punto en lugar de coma para evitar errores de BD que mandan al océano (0,0)
                                    $lat = str_replace(',', '.', (string) $state['lat']);
                                    $lng = str_replace(',', '.', (string) $state['lng']);
                                    $set('latitud_capturada', $lat);
                                    $set('longitud_capturada', $lng);
                                }
                            })
                            ->afterStateHydrated(function ($get, $set, $record): void {
                                if ($record && $record->latitud_capturada && $record->longitud_capturada) {
                                    $set('ubicacion_gps', ['lat' => $record->latitud_capturada, 'lng' => $record->longitud_capturada]);
                                }
                            })
                            ->live(onBlur: true)
                            ->dehydrated(false),
                        
                        // Campos invisibles reales que guardan en la BD
                        \Filament\Forms\Components\Hidden::make('latitud_capturada'),
                        \Filament\Forms\Components\Hidden::make('longitud_capturada'),
                    ]),
            ]);
    }
}
