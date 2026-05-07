<?php

namespace App\Filament\Resources\Tickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID compacto
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->prefix('#')
                    ->weight(FontWeight::Bold)
                    ->width('60px'),

                // Título
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(60)
                    ->wrap(),

                // Categoría IA en lugar de departamento
                TextColumn::make('ia_categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color('warning')
                    ->placeholder('—'),

                // Solicitante
                TextColumn::make('cliente.nombre_completo')
                    ->label('Solicitante')
                    ->searchable()
                    ->placeholder('—'),

                // Técnico Asignado (Assignee)
                TextColumn::make('tecnico.name')
                    ->label('Asignado a')
                    ->searchable()
                    ->placeholder('Sin Asignar')
                    ->icon('heroicon-m-user-circle'),

                // Estado con badge de color
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Abierto'    => 'danger',
                        'En Proceso' => 'warning',
                        'Resuelto'   => 'success',
                        'Cerrado'    => 'gray',
                        default      => 'gray',
                    }),

                // Prioridad IA
                TextColumn::make('ia_prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Alta'  => 'danger',
                        'Media' => 'warning',
                        'Baja'  => 'success',
                        default => 'gray',
                    })
                    ->placeholder('—'),

                // Fecha de última actividad
                TextColumn::make('updated_at')
                    ->label('Última Actividad')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Abierto'    => 'Abierto',
                        'En Proceso' => 'En Proceso',
                        'Resuelto'   => 'Resuelto',
                        'Cerrado'    => 'Cerrado',
                    ]),

                SelectFilter::make('ia_prioridad')
                    ->label('Prioridad IA')
                    ->options([
                        'Alta'  => 'Alta',
                        'Media' => 'Media',
                        'Baja'  => 'Baja',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Técnico')
                    ->relationship('tecnico', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
