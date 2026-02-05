<?php

namespace Modules\CRM\Http\Controllers\Api;

use Modules\CRM\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends BaseController
{
    /**
     * Listar leads.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Listado de leads',
        ]);
    }

    /**
     * Almacenar nuevo lead.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Lead creado exitosamente',
        ], 201);
    }

    /**
     * Mostrar lead.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del lead',
        ]);
    }

    /**
     * Actualizar lead.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Lead actualizado exitosamente',
        ]);
    }

    /**
     * Eliminar lead.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Lead eliminado exitosamente',
        ]);
    }
}
