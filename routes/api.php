<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\MunicipalServiceController;
use App\Http\Controllers\TicketApiController;
use Illuminate\Support\Facades\Route;

// 1. RUTA PÚBLICA: El usuario se registra de forma segura y obtiene su Token (Bearer/JWT)
Route::post('/v1/municipal/registro', [AuthApiController::class, 'registrar']);

// 2. RUTAS PRIVADAS Y PROTEGIDAS: Solo accesibles si se envía el Token de seguridad válido
Route::middleware('auth:sanctum')->group(function () {
    
    // Consumo de WebService Externo (Solo para personal municipal autenticado)
    Route::get('/v1/municipal/indicadores', [MunicipalServiceController::class, 'obtenerIndicadoresEconomicos']);
    
    // Lógica de Negocio y Persistencia (Crea el ticket enlazado al usuario real logueado)
    Route::post('/v1/municipal/tickets', [TicketApiController::class, 'crearTicket']);
    
});

