<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\MunicipalServiceController;
use App\Http\Controllers\TicketApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/municipal')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Rutas públicas
    |--------------------------------------------------------------------------
    */

    Route::post('/registro', [
        AuthApiController::class,
        'registrar',
    ]);

    Route::post('/login', [
        AuthApiController::class,
        'login',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Rutas protegidas con Laravel Sanctum
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/usuario', [
            AuthApiController::class,
            'usuario',
        ]);

        Route::post('/logout', [
            AuthApiController::class,
            'logout',
        ]);

        /*
        | Administradores y supervisores.
        */
        Route::get('/indicadores', [
            MunicipalServiceController::class,
            'obtenerIndicadoresEconomicos',
        ])->middleware('rol:administrador,supervisor');

        /*
        | Todos los roles municipales.
        */
        Route::post('/tickets', [
            TicketApiController::class,
            'crearTicket',
        ])->middleware(
            'rol:administrador,supervisor,operador'
        );

        /*
        | Ruta temporal para comprobar el rol administrador.
        */
        Route::get(
            '/admin/verificar',
            function (Request $request) {
                return response()->json([
                    'mensaje' => 'Acceso de administrador autorizado.',
                    'usuario' => [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'role' => $request->user()->role,
                    ],
                ]);
            }
        )->middleware('rol:administrador');
    });
});