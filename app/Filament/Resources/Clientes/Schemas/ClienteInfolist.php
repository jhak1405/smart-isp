<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información Personal')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    TextEntry::make('nombre_completo')
                        ->label('Nombre Completo')
                        ->weight('bold')
                        ->size('lg')
                        ->columnSpanFull(),

                    TextEntry::make('dni_ruc')
                        ->label('DNI / RUC')
                        ->icon('heroicon-m-finger-print'),

                    TextEntry::make('telefono')
                        ->label('Teléfono')
                        ->icon('heroicon-m-phone')
                        ->placeholder('—'),

                    TextEntry::make('estado')
                        ->label('Estado del Servicio')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'Activo'     => 'success',
                            'Inactivo'   => 'danger',
                            'Suspendido' => 'warning',
                            default      => 'gray',
                        }),
                ]),

            Section::make('Ubicación')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->schema([
                    TextEntry::make('direccion_escrita')
                        ->label('Dirección')
                        ->placeholder('Sin dirección registrada.')
                        ->columnSpanFull(),

                    TextEntry::make('latitud')
                        ->label('Latitud')
                        ->placeholder('—'),

                    TextEntry::make('longitud')
                        ->label('Longitud')
                        ->placeholder('—'),
                ]),

            Section::make('Foto de Fachada')
                ->icon('heroicon-o-camera')
                ->description('Foto de referencia de la vivienda del cliente, capturada en campo por el técnico.')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('foto_fachada_img')
                        ->label('')
                        ->columnSpanFull()
                        ->content(function ($record) {
                            if (blank($record->foto_fachada)) {
                                return new \Illuminate\Support\HtmlString(
                                    '<p style="color:#6b7280;font-size:0.875rem;">No se ha registrado foto de fachada aún. El técnico la tomará en la próxima visita.</p>'
                                );
                            }
                            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->foto_fachada);
                            return new \Illuminate\Support\HtmlString(
                                '<a href="' . $url . '" target="_blank">
                                    <img src="' . $url . '" style="max-height:320px;width:100%;object-fit:cover;border-radius:10px;border:1px solid #374151;display:block;" alt="Foto de fachada">
                                </a>'
                            );
                        }),
                ]),
        ]);
    }
}
