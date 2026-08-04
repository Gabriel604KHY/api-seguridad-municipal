<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Verificar que el usuario autenticado tenga uno de los roles permitidos.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response|JsonResponse {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Debes iniciar sesión para acceder a este recurso.',
            ], 401);
        }

        if (!$usuario->tieneRol($roles)) {
            return response()->json([
                'mensaje' => 'No tienes permisos para realizar esta acción.',
                'rol_actual' => $usuario->role,
                'roles_permitidos' => $roles,
            ], 403);
        }

        return $next($request);
    }
}