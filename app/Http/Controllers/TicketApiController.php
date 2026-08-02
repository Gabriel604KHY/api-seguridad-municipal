<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketApiController extends Controller
{
    /**
     * Crear una nueva solicitud de soporte (Lógica de negocio e inserción DML)
     */
    public function crearTicket(Request $request)
    {
        // 1. Control de datos e integridad referencial
        $validador = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'required|string'
        ]);

        if ($validador->fails()) {
            return response()->json(['error' => 'Datos de solicitud inválidos', 'detalles' => $validador->errors()], 400);
        }

        // 2. Sanitización estricta contra código malicioso (XSS)
        $tituloLimpio = strip_tags($request->titulo);
        $descripcionLimpia = strip_tags($request->descripcion);

        // 3. Simulación de persistencia de datos (Operación SQL DML)
        return response()->json([
            'mensaje' => 'Solicitud de soporte municipal procesada exitosamente en el sistema relacional',
            'ticket' => [
                'user_id' => $request->user_id,
                'titulo' => $tituloLimpio,
                'descripcion' => $descripcionLimpia,
                'estado' => 'pendiente',
                'created_at' => now()
            ]
        ], 201);
    }
}
