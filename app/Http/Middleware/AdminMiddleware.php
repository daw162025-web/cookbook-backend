<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si el usuario está logueado y si su rol es admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        //Si no es admin, devolvemos un 403
        return response()->json(['message' => 'Acceso denegado. No tienes permisos de administrador.'], 403);
    }
}
