<?php

namespace App\Filament\Resources\Pendientes;

use App\Filament\Resources\Pendientes\Pages\ListPendientes;
use App\Models\Pendiente;
use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PendienteResource extends Resource
{
    protected static ?string $model = Pendiente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Soporte Técnico';

    protected static ?string $navigationLabel = 'Pendientes';

    protected static ?string $modelLabel = 'Pendiente';

    protected static ?string $pluralModelLabel = 'Pendientes';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('tipo')
                    ->label('Tipo de Pendiente')
                    ->required()
                    ->placeholder('Ej: Cambio de router, Verificación de cableado...')
                    ->columnSpanFull(),

                Select::make('estado')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'Pendiente'  => 'Pendiente',
                        'Completado' => 'Completado',
                    ])
                    ->default('Pendiente')
                    ->native(false),

                DatePicker::make('fecha_recordatorio')
                    ->label('Fecha de Recordatorio')
                    ->required()
                    ->minDate(now()->toDateString())
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                Select::make('user_id')
                    ->label('Técnico Asignado')
                    ->relationship('tecnico', 'name', fn (Builder $query) => $query->where('role', 'Técnico'))
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->placeholder('Sin asignar')
                    ->columnSpanFull(),

                Textarea::make('descripcion')
                    ->label('Descripción / Notas')
                    ->rows(3)
                    ->placeholder('Detalles adicionales del pendiente...')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_recordatorio')
                    ->label('Fecha')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Pendiente $record): string => match (true) {
                        $record->is_vencido => 'danger',
                        $record->is_hoy    => 'warning',
                        default            => 'gray',
                    }),

                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('semibold')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('tecnico.name')
                    ->label('Asignado a')
                    ->placeholder('Sin asignar')
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente'  => 'warning',
                        'Completado' => 'success',
                        default      => 'gray',
                    }),
            ])
            ->defaultSort('fecha_recordatorio', 'asc')
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Pendiente'  => 'Pendiente',
                        'Completado' => 'Completado',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Técnico')
                    ->relationship('tecnico', 'name'),
            ])
            ->recordActions([
                EditAction::make()->modalWidth('md'),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPendientes::route('/'),
        ];
    }
}
