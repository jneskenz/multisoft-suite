<?php

namespace Modules\Core\Http\Controllers\Api;

use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * Listar roles.
     */
    public function index(): JsonResponse
    {
        // TODO: Implementar lógica de listado
        return response()->json([
            'data' => [],
            'message' => 'Listado de roles',
        ]);
    }

    /**
     * Almacenar nuevo rol.
     */
    public function store(Request $request): JsonResponse
    {
        // TODO: Implementar lógica de creación
        return response()->json([
            'data' => null,
            'message' => 'Rol creado exitosamente',
        ], 201);
    }

    /**
     * Mostrar rol.
     */
    public function show(string $id): JsonResponse
    {
        // TODO: Implementar lógica de obtención
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del rol',
        ]);
    }

    /**
     * Actualizar rol.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // TODO: Implementar lógica de actualización
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Rol actualizado exitosamente',
        ]);
    }

    /**
     * Eliminar rol.
     */
    public function destroy(string $id): JsonResponse
    {
        // TODO: Implementar lógica de eliminación
        return response()->json([
            'message' => 'Rol eliminado exitosamente',
        ]);
    }
}
