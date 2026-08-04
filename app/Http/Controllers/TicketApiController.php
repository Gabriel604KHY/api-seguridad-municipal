<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    /**
     * Listar los tickets pertenecientes al usuario autenticado.
     */
    public function listarTickets(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Debes iniciar sesión para consultar los tickets.',
            ], 401);
        }

        $datos = $request->validate([
            'estado' => [
                'nullable',
                'string',
                'in:abierto,en_proceso,resuelto,cerrado',
            ],
            'prioridad' => [
                'nullable',
                'string',
                'in:baja,media,alta,critica',
            ],
            'categoria' => [
                'nullable',
                'string',
                'max:50',
            ],
            'buscar' => [
                'nullable',
                'string',
                'max:150',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $consulta = Ticket::query()
            ->where('user_id', $usuario->id);

        if (!empty($datos['estado'])) {
            $consulta->where(
                'estado',
                $datos['estado']
            );
        }

        if (!empty($datos['prioridad'])) {
            $consulta->where(
                'prioridad',
                $datos['prioridad']
            );
        }

        if (!empty($datos['categoria'])) {
            $consulta->where(
                'categoria',
                $datos['categoria']
            );
        }

        if (!empty($datos['buscar'])) {
            $buscar = trim($datos['buscar']);

            $consulta->where(
                function (Builder $query) use ($buscar): void {
                    $query
                        ->where(
                            'titulo',
                            'like',
                            "%{$buscar}%"
                        )
                        ->orWhere(
                            'descripcion',
                            'like',
                            "%{$buscar}%"
                        )
                        ->orWhere(
                            'ubicacion',
                            'like',
                            "%{$buscar}%"
                        );
                }
            );
        }

        $porPagina = $datos['per_page'] ?? 10;

        $resultado = $consulta
            ->latest()
            ->paginate($porPagina);

        return response()->json([
            'mensaje' => 'Tickets obtenidos correctamente.',
            'tickets' => $resultado->items(),
            'meta' => [
                'pagina_actual' => $resultado->currentPage(),
                'ultima_pagina' => $resultado->lastPage(),
                'por_pagina' => $resultado->perPage(),
                'total' => $resultado->total(),
            ],
        ]);
    }

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

        $ticket = new Ticket();

        $ticket->user_id = $usuario->id;
        $ticket->titulo = $datos['titulo'];
        $ticket->descripcion = $datos['descripcion'];
        $ticket->categoria = $datos['categoria'] ?: 'general';
        $ticket->prioridad = $datos['prioridad']
            ?: Ticket::PRIORIDAD_MEDIA;
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
                'updated_at' => $ticket->updated_at,
            ],
        ], 201);
    }
}