<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as FilamentLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class Login extends FilamentLogin
{
    /**
     * Overrides Filament's authenticate() method.
     * 
     * Before running Filament's default flow (which blocks non-admins via canAccessPanel),
     * we check if the user is a Técnico and redirect them to their own dashboard.
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // Check if the user exists and verify password manually first
        $user = \App\Models\User::where('email', $data['email'])->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
            // Valid credentials — check if they are a Técnico (not an Administrador)
            if ($user->role !== 'Administrador') {
                // Log them in via Laravel auth (bypassing canAccessPanel)
                Auth::login($user, $data['remember'] ?? false);
                session()->regenerate();

                // Redirect to the technician dashboard
                $this->redirect('/tecnico');
                return null;
            }
        }

        // For Administrators, use the standard Filament authentication flow
        return parent::authenticate();
    }
}
