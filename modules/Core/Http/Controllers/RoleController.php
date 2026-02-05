<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * Listar roles.
     */
    public function index()
    {
        return view('core::roles.index');
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('core::roles.create');
    }

    /**
     * Almacenar nuevo rol.
     */
    public function store(Request $request)
    {
        // TODO: Implementar lógica de creación
        return redirect()->route('core.roles.index')
            ->with('success', __('Rol creado exitosamente'));
    }

    /**
     * Mostrar rol.
     */
    public function show(string $id)
    {
        return view('core::roles.show', compact('id'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(string $id)
    {
        return view('core::roles.edit', compact('id'));
    }

    /**
     * Actualizar rol.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementar lógica de actualización
        return redirect()->route('core.roles.index')
            ->with('success', __('Rol actualizado exitosamente'));
    }

    /**
     * Eliminar rol.
     */
    public function destroy(string $id)
    {
        // TODO: Implementar lógica de eliminación
        return redirect()->route('core.roles.index')
            ->with('success', __('Rol eliminado exitosamente'));
    }
}
