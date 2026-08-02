<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * Registro seguro de usuarios municipales (Sanitización y mitigación XSS/Inyecciones)
     */
    public function registrar(Request $request)
    {
        // 1. Validación estricta contra inyecciones de datos
        $validador = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validador->fails()) {
            return response()->json([
                'error' => 'Error de validación de datos',
                'mensajes' => $validador->errors()
            ], 400);
        }

        // 2. Sanitización explícita contra ataques Cross-Site Scripting (XSS)
        $nombreSanitizado = strip_tags($request->name);

        // 3. Persistencia segura con encriptación hash (Integridad de datos)
        $usuario = User::create([
            'name' => $nombreSanitizado,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 4. Emisión de Token de acceso seguro (Implementación tipo JWT/Bearer)
        $token = $usuario->createToken('auth_token_municipal')->plainTextToken;

        return response()->json([
            'mensaje' => 'Usuario registrado exitosamente en el sistema de soporte municipal',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}
