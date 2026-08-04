<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    /**
     * Crear un ticket municipal y asociarlo al usuario autenticado.
     */
    public function crearTicket(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Debes iniciar sesión para crear un ticket.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Normalización de datos
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'titulo' => trim(
                strip_tags((string) $request->input('titulo'))
            ),
            'descripcion' => trim(
                strip_tags((string) $request->input('descripcion'))
            ),
            'categoria' => strtolower(
                trim((string) $request->input('categoria', 'general'))
            ),
            'prioridad' => strtolower(
                trim((string) $request->input('prioridad', 'media'))
            ),
            'ubicacion' => trim(
                strip_tags((string) $request->input('ubicacion', ''))
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        $datos = $request->validate([
            'titulo' => [
                'required',
                'string',
                'min:3',
                'max:150',
            ],
            'descripcion' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],
            'categoria' => [
                'nullable',
                'string',
                'max:50',
            ],
            'prioridad' => [
                'nullable',
                'string',
                'in:baja,media,alta,critica',
            ],
            'ubicacion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Creación del ticket
        |--------------------------------------------------------------------------
        |
        | user_id se obtiene del token de Sanctum. El cliente no puede elegir
        | el usuario propietario del ticket.
        |
        */

        $ticket = new Ticket();

        $ticket->user_id = $usuario->id;
        $ticket->titulo = $datos['titulo'];
        $ticket->descripcion = $datos['descripcion'];
        $ticket->categoria = $datos['categoria'] ?: 'general';
        $ticket->prioridad = $datos['prioridad'] ?: Ticket::PRIORIDAD_MEDIA;
        $ticket->estado = Ticket::ESTADO_ABIERTO;
        $ticket->ubicacion = $datos['ubicacion'] ?: null;

        $ticket->save();

        return response()->json([
            'mensaje' => 'Ticket municipal creado correctamente.',
            'ticket' => [
                'id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'titulo' => $ticket->titulo,
                'descripcion' => $ticket->descripcion,
                'categoria' => $ticket->categoria,
                'prioridad' => $ticket->prioridad,
                'estado' => $ticket->estado,
                'ubicacion' => $ticket->ubicacion,
                'created_at' => $ticket->created_at,
            ],
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $usuario->role,
            ],
        ], 201);
    }
}