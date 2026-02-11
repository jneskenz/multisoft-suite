<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Modules\HR\Models\Empleado;

class EmpleadoController extends BaseController
{
    /**
     * Listar empleados.
     */
    public function index()
    {
        return view('hr::empleados.index');
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('hr::empleados.create');
    }

    /**
     * Almacenar nuevo empleado.
     */
    public function store(Request $request)
    {
        // TODO: Implementar lógica de creación
        return redirect()->route('hr.empleados.index')
            ->with('success', __('Empleado creado exitosamente'));
    }

    /**
     * Mostrar empleado.
     */
    public function show(string $id)
    {
        return view('hr::empleados.show', compact('id'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(string $id)
    {
        return view('hr::empleados.edit', compact('id'));
    }

    /**
     * Actualizar empleado.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementar lógica de actualización
        return redirect()->route('hr.empleados.index')
            ->with('success', __('Empleado actualizado exitosamente'));
    }

    /**
     * Eliminar empleado.
     */
    public function destroy(string $id)
    {
        // TODO: Implementar lógica de eliminación
        return redirect()->route('hr.empleados.index')
            ->with('success', __('Empleado eliminado exitosamente'));
    }
}
