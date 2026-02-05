<?php

namespace Modules\Reports\Http\Controllers\Api;

use Modules\Reports\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends BaseController
{
    /**
     * Listar reportes disponibles.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Listado de reportes',
        ]);
    }

    /**
     * Generar reporte.
     */
    public function generate(Request $request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Reporte generado exitosamente',
        ]);
    }

    /**
     * Mostrar reporte específico.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del reporte',
        ]);
    }

    /**
     * Exportar reporte.
     */
    public function export(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id, 'format' => $request->input('format', 'pdf')],
            'message' => 'Reporte exportado exitosamente',
        ]);
    }
}
