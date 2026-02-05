<?php

namespace Modules\Partners\Http\Controllers\Api;

use Modules\Partners\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends BaseController
{
    /**
     * Listar partners.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Listado de partners',
        ]);
    }

    /**
     * Almacenar nuevo partner.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Partner creado exitosamente',
        ], 201);
    }

    /**
     * Mostrar partner.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del partner',
        ]);
    }

    /**
     * Actualizar partner.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Partner actualizado exitosamente',
        ]);
    }

    /**
     * Eliminar partner.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Partner eliminado exitosamente',
        ]);
    }
}
