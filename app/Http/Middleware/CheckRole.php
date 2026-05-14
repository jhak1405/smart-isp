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
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Los administradores no deben acceder a rutas de técnico
        if (auth()->user()->role === 'Administrador') {
            return redirect('/admin');
        }

        return $next($request);
    }
}
