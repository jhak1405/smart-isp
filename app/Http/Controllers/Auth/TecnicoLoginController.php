<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TecnicoLoginController extends Controller
{
    /**
     * Muestra el formulario de login para técnicos.
     */
    public function showForm()
    {
        // Si ya está logueado, redirigir al lugar correcto
        if (Auth::check()) {
            return Auth::user()->role === 'Administrador'
                ? redirect('/admin')
                : redirect('/tecnico');
        }

        return view('auth.tecnico-login');
    }

    /**
     * Procesa el login del técnico con Laravel Auth directamente,
     * sin pasar por la lógica de canAccessPanel() de Filament.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirigir según el rol
            if ($user->role === 'Administrador') {
                return redirect('/admin');
            }

            if ($user->role === 'Técnico') {
                return redirect('/tecnico');
            }

            // Rol desconocido: cerrar sesión por seguridad
            Auth::logout();
            return back()->withErrors(['email' => 'Tu cuenta no tiene un rol válido asignado.']);
        }

        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no son correctas.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
