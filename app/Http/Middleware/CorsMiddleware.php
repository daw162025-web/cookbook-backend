<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [
            'http://localhost:4200',
            'https://cookbook-frontend-eta.vercel.app',
        ];

        $origin = $request->headers->get('Origin');

        if ($request->isMethod('OPTIONS')) {
            $response = response()->json('OK', 200);
            $response->headers->remove('Access-Control-Allow-Origin');
            $response->headers->set('Access-Control-Allow-Origin', in_array($origin, $allowedOrigins) ? $origin : '');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Vary', 'Origin'); // ← clave para CDN
            return $response;
        }

        $response = $next($request);
        $response->headers->remove('Access-Control-Allow-Origin');
        $response->headers->set('Access-Control-Allow-Origin', in_array($origin, $allowedOrigins) ? $origin : '');
        $response->headers->set('Vary', 'Origin'); // ← clave para CDN
        return $response;
    }
}
