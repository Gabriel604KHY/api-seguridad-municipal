<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class MunicipalServiceController extends Controller
{
    /**
     * Obtener indicadores económicos chilenos desde una API externa.
     */
    public function obtenerIndicadoresEconomicos(): JsonResponse
    {
        try {
            $respuesta = Http::acceptJson()
                ->timeout(15)
                ->retry(2, 500)
                ->get('https://mindicador.cl/api');

            if ($respuesta->failed()) {
                return response()->json([
                    'error' => 'El servicio externo de indicadores respondió con un error.',
                ], 502);
            }

            $datos = $respuesta->json();

            $indicadores = [
                'uf' => data_get($datos, 'uf.valor'),
                'utm' => data_get($datos, 'utm.valor'),
                'dolar' => data_get($datos, 'dolar.valor'),
                'euro' => data_get($datos, 'euro.valor'),
            ];

            $sinDatos = collect($indicadores)
                ->every(
                    fn ($valor): bool => $valor === null
                );

            if ($sinDatos) {
                return response()->json([
                    'error' => 'El servicio externo no entregó indicadores válidos.',
                ], 502);
            }

            return response()->json([
                'mensaje' => 'Indicadores económicos obtenidos correctamente.',
                'origen' => 'mindicador.cl',
                'indicadores' => [
                    'uf' => $indicadores['uf'] ?? 'No disponible',
                    'utm' => $indicadores['utm'] ?? 'No disponible',
                    'dolar' => $indicadores['dolar'] ?? 'No disponible',
                    'euro' => $indicadores['euro'] ?? 'No disponible',
                ],
            ], 200);
        } catch (Throwable $error) {
            report($error);

            return response()->json([
                'error' => 'No fue posible conectar con el servicio externo de indicadores.',
            ], 503);
        }
    }
}