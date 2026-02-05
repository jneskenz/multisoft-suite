<?php

namespace Modules\FMS\Http\Controllers\Api;

use Modules\FMS\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends BaseController
{
    /**
     * Listar cuentas contables.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Listado de cuentas contables',
        ]);
    }

    /**
     * Almacenar nueva cuenta.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Cuenta creada exitosamente',
        ], 201);
    }

    /**
     * Mostrar cuenta.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle de la cuenta',
        ]);
    }

    /**
     * Actualizar cuenta.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Cuenta actualizada exitosamente',
        ]);
    }

    /**
     * Eliminar cuenta.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Cuenta eliminada exitosamente',
        ]);
    }
}
