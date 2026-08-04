<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMINISTRADOR = 'administrador';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_OPERADOR = 'operador';

    /**
     * Campos permitidos para creación masiva.
     *
     * El rol no se incluye para evitar que un usuario pueda
     * registrarse públicamente como administrador.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Campos ocultos en respuestas JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Comprobar si el usuario es administrador.
     */
    public function esAdministrador(): bool
    {
        return $this->role === self::ROLE_ADMINISTRADOR;
    }

    /**
     * Comprobar si el usuario es supervisor.
     */
    public function esSupervisor(): bool
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    /**
     * Comprobar si el usuario es operador.
     */
    public function esOperador(): bool
    {
        return $this->role === self::ROLE_OPERADOR;
    }

    /**
     * Comprobar si el usuario tiene uno de los roles indicados.
     */
    public function tieneRol(string|array $roles): bool
    {
        return in_array(
            $this->role,
            (array) $roles,
            true
        );
    }
}