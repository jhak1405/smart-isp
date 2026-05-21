<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // En Filament Shield se maneja esto por políticas o roles.
        // Pero para separar la app web del técnico, si es Técnico lo mandamos a /tecnico
        if ($this->hasRole('Técnico')) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect('/tecnico'));
        }

        // Si es panel admin, permitimos a todos los demás roles que sí entran a Filament
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['Administrador', 'Coordinador de Soporte', 'Personal Administrativo', 'super_admin']);
        }
        
        return true;
    }
}
