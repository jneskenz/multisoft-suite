<?php

namespace Modules\HR\Http\Controllers\Api;

use Modules\HR\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends BaseController
{
    /**
     * Listar empleados.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Listado de empleados',
        ]);
    }

    /**
     * Almacenar nuevo empleado.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Empleado creado exitosamente',
        ], 201);
    }

    /**
     * Mostrar empleado.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del empleado',
        ]);
    }

    /**
     * Actualizar empleado.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Empleado actualizado exitosamente',
        ]);
    }

    /**
     * Eliminar empleado.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Empleado eliminado exitosamente',
        ]);
    }
}
