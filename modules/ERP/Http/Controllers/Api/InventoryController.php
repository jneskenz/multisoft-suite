<?php

namespace Modules\ERP\Http\Controllers\Api;

use Modules\ERP\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends BaseController
{
    /**
     * Listar productos del inventario.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Listado de inventario',
        ]);
    }

    /**
     * Almacenar nuevo producto.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Producto creado exitosamente',
        ], 201);
    }

    /**
     * Mostrar producto.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Detalle del producto',
        ]);
    }

    /**
     * Actualizar producto.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => ['id' => $id],
            'message' => 'Producto actualizado exitosamente',
        ]);
    }

    /**
     * Eliminar producto.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Producto eliminado exitosamente',
        ]);
    }
}
