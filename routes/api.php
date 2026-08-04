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
        |--------------------------------------------------------------------------
        | Indicadores
        |--------------------------------------------------------------------------
        | Acceso para administradores y supervisores.
        */

        Route::get('/indicadores', [
            MunicipalServiceController::class,
            'obtenerIndicadoresEconomicos',
        ])->middleware(
            'rol:administrador,supervisor'
        );

        /*
        |--------------------------------------------------------------------------
        | Tickets
        |--------------------------------------------------------------------------
        | Acceso para administradores, supervisores y operadores.
        */

        Route::get('/tickets', [
            TicketApiController::class,
            'listarTickets',
        ])->middleware(
            'rol:administrador,supervisor,operador'
        );

        Route::post('/tickets', [
            TicketApiController::class,
            'crearTicket',
        ])->middleware(
            'rol:administrador,supervisor,operador'
        );

        /*
        |--------------------------------------------------------------------------
        | Verificación administrativa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/verificar',
            function (Request $request) {
                $usuario = $request->user();

                return response()->json([
                    'mensaje' => 'Acceso de administrador autorizado.',
                    'usuario' => [
                        'id' => $usuario->id,
                        'name' => $usuario->name,
                        'email' => $usuario->email,
                        'role' => $usuario->role,
                    ],
                ]);
            }
        )->middleware('rol:administrador');
    });
});