<?php

namespace App\Filament\Resources\Pendientes\Pages;

use App\Filament\Resources\Pendientes\PendienteResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListPendientes extends ListRecords
{
    protected static string $resource = PendienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Pendiente')
                ->modalWidth('md')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['creado_por'] = auth()->id();
                    return $data;
                }),
        ];
    }
}
