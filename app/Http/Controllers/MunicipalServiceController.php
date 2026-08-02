<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MunicipalServiceController extends Controller
{
    /**
     * Consumo de API externa para obtener indicadores económicos del día
     * Demuestra el uso de WebServices de terceros solicitado en el perfil de cargo.
     */
    public function obtenerIndicadoresEconomicos()
    {
        // Consumo asíncrono y seguro de un servicio externo público chileno
        $respuesta = Http::get('https://mindicador.cl');

        if ($respuesta->failed()) {
            return response()->json([
                'error' => 'No se pudo conectar con el WebService externo de indicadores financieros'
            ], 500);
        }

        $datos = $respuesta->json();

        // Estructuración de datos limpios para el uso de dependencias municipales
        return response()->json([
            'mensaje' => 'Datos financieros obtenidos exitosamente mediante API de terceros',
            'origen' => 'mindicador.cl',
            'indicadores' => [
                'uf' => $datos['uf']['valor'] ?? 'No disponible',
                'utm' => $datos['utm']['valor'] ?? 'No disponible',
                'dolar' => $datos['dolar']['valor'] ?? 'No disponible',
                'euro' => $datos['euro']['valor'] ?? 'No disponible'
            ]
        ], 200);
    }
}
