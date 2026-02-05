<?php

namespace Modules\Core\Http\Controllers\Api;

use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * Listar usuarios.
     */
    public function index(): JsonResponse
    {
        // TODO: Implementar lógica de listado
        return response()->json([
            'data' => [],
            'message' => 'Listado de usuarios',
        ]);
    }

    /**
     * Almacenar nuevo usuario.
     */
    public function store(Request $request): JsonResponse
    {
        // TODO: Implementar lógica de creación
        return response()->json([
            'data' => null,
            'message' => 'Usuario creado exitosamente',
        ], 201);
    }

    /**
     * Mostrar usuario.
     */
    public function show(string $id): JsonResponse
    {
        // TODO: Implementar lógica de obtención
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del usuario',
        ]);
    }

    /**
     * Actualizar usuario.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // TODO: Implementar lógica de actualización
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Usuario actualizado exitosamente',
        ]);
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(string $id): JsonResponse
    {
        // TODO: Implementar lógica de eliminación
        return response()->json([
            'message' => 'Usuario eliminado exitosamente',
        ]);
    }
}
