<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = 'Tecnico'): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (auth()->user()->role !== $role) {
            if (auth()->user()->role === 'Administrador') {
                return redirect('/admin');
            }
            abort(403, 'No tienes permisos para acceder a esta área.');
        }

        return $next($request);
    }
}
