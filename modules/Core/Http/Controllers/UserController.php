<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * Listar usuarios.
     */
    public function index()
    {
        return view('core::users.index');
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('core::users.create');
    }

    /**
     * Almacenar nuevo usuario.
     */
    public function store(Request $request)
    {
        // TODO: Implementar lógica de creación
        return redirect()->route('core.users.index')
            ->with('success', __('Usuario creado exitosamente'));
    }

    /**
     * Mostrar usuario.
     */
    public function show(string $id)
    {
        return view('core::users.show', compact('id'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(string $id)
    {
        return view('core::users.edit', compact('id'));
    }

    /**
     * Actualizar usuario.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementar lógica de actualización
        return redirect()->route('core.users.index')
            ->with('success', __('Usuario actualizado exitosamente'));
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(string $id)
    {
        // TODO: Implementar lógica de eliminación
        return redirect()->route('core.users.index')
            ->with('success', __('Usuario eliminado exitosamente'));
    }
}
