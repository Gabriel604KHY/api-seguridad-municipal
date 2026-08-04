<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthApiController extends Controller
{
    /**
     * Registrar un nuevo usuario municipal.
     */
    public function registrar(Request $request): JsonResponse
    {
        // Normalizar los datos antes de validarlos.
        $request->merge([
            'name' => trim(
                strip_tags((string) $request->input('name'))
            ),
            'email' => strtolower(
                trim((string) $request->input('email'))
            ),
        ]);

        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make(
                $datos['password']
            ),
        ]);

        $nombreToken = $datos['device_name']
            ?? 'api-seguridad-municipal';

        $token = $usuario
            ->createToken($nombreToken)
            ->plainTextToken;

        return response()->json([
            'mensaje' => 'Usuario registrado correctamente.',
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Iniciar sesión y generar un token de acceso.
     */
    public function login(Request $request): JsonResponse
    {
        $request->merge([
            'email' => strtolower(
                trim((string) $request->input('email'))
            ),
        ]);

        $datos = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

$usuario = User::query()
    ->where('email', $datos['email'])
    ->first();

        if (
            !$usuario ||
            !Hash::check(
                $datos['password'],
                $usuario->password
            )
        ) {
            return response()->json([
                'mensaje' => 'El correo o la contraseña no son correctos.',
            ], 401);
        }

        $nombreToken = $datos['device_name']
            ?? 'api-seguridad-municipal';

        $token = $usuario
            ->createToken($nombreToken)
            ->plainTextToken;

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso.',
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Obtener los datos del usuario autenticado.
     */
    public function usuario(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no autenticado.',
            ], 401);
        }

        return response()->json([
            'mensaje' => 'Usuario autenticado correctamente.',
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'created_at' => $usuario->created_at,
                'updated_at' => $usuario->updated_at,
            ],
        ], 200);
    }

    /**
     * Cerrar sesión y eliminar el token utilizado.
     */
    public function logout(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no autenticado.',
            ], 401);
        }

        $tokenActual = $usuario->currentAccessToken();

        if ($tokenActual instanceof PersonalAccessToken) {
            $tokenActual->delete();
        }

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente.',
        ], 200);
    }
}