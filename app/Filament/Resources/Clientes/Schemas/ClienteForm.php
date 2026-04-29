<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->description('Datos de identificación del abonado.')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre_completo')
                            ->label('Nombre Completo')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Pérez García')
                            ->columnSpanFull(),

                        TextInput::make('dni_ruc')
                            ->label('DNI / RUC')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->rules(['digits:8'])
                            ->placeholder('Ej: 12345678'),

                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->numeric()
                            ->rules(['digits:9'])
                            ->placeholder('Ej: 987654321'),

                        Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->options([
                                'Activo'     => 'Activo',
                                'Inactivo'   => 'Inactivo',
                                'Suspendido' => 'Suspendido',
                            ])
                            ->default('Activo')
                            ->native(false),
                    ]),

                Section::make('Ubicación')
                    ->description('Dirección física y coordenadas GPS del abonado.')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Textarea::make('direccion_escrita')
                            ->label('Dirección')
                            ->rows(3)
                            ->placeholder('Ej: Av. Los Álamos 123, Urbanización Las Flores')
                            ->columnSpanFull(),

                        TextInput::make('latitud')
                            ->label('Latitud')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step(0.00000001)
                            ->placeholder('Ej: -12.04318000')
                            ->helperText('Se completará automáticamente desde el móvil (Sprint 3).'),

                        TextInput::make('longitud')
                            ->label('Longitud')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step(0.00000001)
                            ->placeholder('Ej: -77.02824100')
                            ->helperText('Se completará automáticamente desde el móvil (Sprint 3).'),
                    ]),
            ]);
    }
}
