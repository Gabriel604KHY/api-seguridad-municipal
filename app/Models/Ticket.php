<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    public const ESTADO_ABIERTO = 'abierto';
    public const ESTADO_EN_PROCESO = 'en_proceso';
    public const ESTADO_RESUELTO = 'resuelto';
    public const ESTADO_CERRADO = 'cerrado';

    public const PRIORIDAD_BAJA = 'baja';
    public const PRIORIDAD_MEDIA = 'media';
    public const PRIORIDAD_ALTA = 'alta';
    public const PRIORIDAD_CRITICA = 'critica';

    /**
     * Campos que pueden asignarse desde el controlador.
     *
     * user_id no se incluye porque se obtendrá del usuario
     * autenticado mediante el token de Sanctum.
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'categoria',
        'prioridad',
        'estado',
        'ubicacion',
        'cerrado_at',
    ];

    /**
     * Conversión automática de atributos.
     */
    protected function casts(): array
    {
        return [
            'cerrado_at' => 'datetime',
        ];
    }

    /**
     * Usuario que creó el ticket.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}