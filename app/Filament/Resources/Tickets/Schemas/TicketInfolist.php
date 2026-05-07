<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // ==========================================
                // COLUMNA IZQUIERDA (2/3) — Info técnica
                // ==========================================
                Group::make([
                    // --- Cabecera: Estado + Prioridad ---
                    Section::make()
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('estado')
                                    ->label('Estado del Ticket')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn (string $state): string => match ($state) {
                                        'Abierto'    => 'danger',
                                        'En Proceso' => 'warning',
                                        'Resuelto'   => 'success',
                                        'Cerrado'    => 'gray',
                                        default      => 'gray',
                                    }),

                                TextEntry::make('ia_prioridad')
                                    ->label('Prioridad IA')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn (?string $state): string => match ($state) {
                                        'Alta'  => 'danger',
                                        'Media' => 'warning',
                                        'Baja'  => 'success',
                                        default => 'gray',
                                    })
                                    ->placeholder('Sin clasificar'),
                            ]),

                            TextEntry::make('titulo')
                                ->label('Asunto de la Incidencia')
                                ->size('lg')
                                ->weight('bold')
                                ->columnSpanFull(),

                            TextEntry::make('tecnico.name')
                                ->label('Técnico Responsable')
                                ->icon('heroicon-m-user-circle')
                                ->placeholder('Sin asignar'),

                            TextEntry::make('created_at')
                                ->label('Registrado el')
                                ->icon('heroicon-m-calendar-days')
                                ->dateTime('d \d\e F \d\e Y, H:i'),
                        ]),

                    // --- Descripción del Problema ---
                    Section::make('Descripción del Problema')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextEntry::make('descripcion')
                                ->label('')
                                ->prose()
                                ->columnSpanFull(),
                        ]),

                    // --- Notas de Equipamiento (Alerta para el técnico) ---
                    Section::make('⚠️ Notas de Equipamiento — Antes de Salir')
                        ->icon('heroicon-o-wrench')
                        ->description('Revisa estos materiales antes de ir al cliente.')
                        ->schema([
                            TextEntry::make('notas_equipamiento')
                                ->label('')
                                ->prose()
                                ->color('warning')
                                ->columnSpanFull()
                                ->placeholder('Sin notas de equipamiento especificadas.'),
                        ])
                        ->hidden(fn ($record) => blank($record?->notas_equipamiento)),

                    // --- Análisis de IA ---
                    Section::make('Análisis de Inteligencia Artificial')
                        ->icon('heroicon-o-sparkles')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('ia_categoria')
                                    ->label('Categoría Detectada')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('—'),

                                TextEntry::make('ia_prioridad')
                                    ->label('Nivel de Prioridad')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'Alta'  => 'danger',
                                        'Media' => 'warning',
                                        'Baja'  => 'success',
                                        default => 'gray',
                                    })
                                    ->placeholder('—'),
                            ]),

                            TextEntry::make('ia_resumen')
                                ->label('Resumen Generado por Gemini')
                                ->prose()
                                ->placeholder('El análisis de IA aún no ha sido procesado para este ticket.')
                                ->columnSpanFull(),
                        ]),

                    // --- Resolución del Técnico ---
                    Section::make('Resolución en Campo')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->collapsed()
                        ->schema([
                            TextEntry::make('nota_tecnico')
                                ->label('Informe del Técnico')
                                ->prose()
                                ->placeholder('Sin notas registradas.')
                                ->columnSpanFull(),

                            ImageEntry::make('evidencia')
                                ->label('Evidencia Fotográfica')
                                ->disk('public')
                                ->visibility('public')
                                ->height(200)
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(2),

                // ==========================================
                // COLUMNA DERECHA (1/3) — Mapa + Cliente
                // ==========================================
                Group::make([
                    // --- Datos del Cliente ---
                    Section::make('Datos del Solicitante')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            TextEntry::make('cliente.nombre_completo')
                                ->label('Nombre Completo')
                                ->weight('bold'),

                            TextEntry::make('cliente.dni_ruc')
                                ->label('DNI / RUC')
                                ->icon('heroicon-m-finger-print')
                                ->placeholder('—'),

                            TextEntry::make('cliente.telefono')
                                ->label('Teléfono')
                                ->icon('heroicon-m-phone')
                                ->placeholder('—'),

                            TextEntry::make('cliente.direccion_escrita')
                                ->label('Dirección')
                                ->icon('heroicon-m-map-pin')
                                ->placeholder('—'),

                            TextEntry::make('cliente.estado')
                                ->label('Estado del Servicio')
                                ->badge()
                                ->color(fn (?string $state): string => match ($state) {
                                    'Activo'    => 'success',
                                    'Inactivo'  => 'danger',
                                    'Suspendido'=> 'warning',
                                    default     => 'gray',
                                }),
                        ]),

                    // --- Mapa de Ubicación ---
                    Section::make('Ubicación')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('mapa_ubicacion')
                                ->label('')
                                ->content(function ($record) {
                                    // Prioridad 1: coordenadas capturadas por el técnico
                                    $lat = $record?->latitud_capturada ?? $record?->cliente?->latitud;
                                    $lng = $record?->longitud_capturada ?? $record?->cliente?->longitud;

                                    return view('components.ticket-map', [
                                        'lat'   => $lat,
                                        'lng'   => $lng,
                                        'label' => $lat && $lng
                                            ? ($record?->latitud_capturada ? 'Ubicación del Técnico' : 'Ubicación del Cliente')
                                            : 'Sin coordenadas',
                                    ]);
                                })
                                ->columnSpanFull(),
                        ]),

                    // --- ID y fechas del ticket ---
                    Section::make('Información del Registro')
                        ->icon('heroicon-o-information-circle')
                        ->collapsed()
                        ->schema([
                            TextEntry::make('id')
                                ->label('ID del Ticket')
                                ->prefix('#')
                                ->badge()
                                ->color('gray'),

                            TextEntry::make('updated_at')
                                ->label('Última Actualización')
                                ->dateTime('d/m/Y H:i'),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
