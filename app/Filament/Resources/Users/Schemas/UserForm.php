<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
                Select::make('role')
                    ->label('Rol del Sistema')
                    ->options([
                        'Administrador' => 'Administrador',
                        'Técnico'       => 'Técnico',
                    ])
                    ->default('Técnico')
                    ->required(),
            ]);
    }
}
