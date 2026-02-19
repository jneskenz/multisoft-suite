<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Modules\HR\Models\Departamento;

class DepartamentoController extends BaseController
{
    /**
     * Listar departamentos.
     */
    public function index()
    {
        return view('hr::departamentos.index');
    }

    /**
     * Mostrar formulario de creacion.
     */
    public function create()
    {
        return view('hr::departamentos.create');
    }

    /**
     * Almacenar nuevo departamento.
     */
    public function store(Request $request)
    {
        // TODO: Implementar logica de creacion
        return redirect()->route('hr.configuracion.departamentos.index')
            ->with('success', __('Departamento creado exitosamente'));
    }

    /**
     * Mostrar departamento.
     */
    public function show(string $id)
    {
        return view('hr::departamentos.show', compact('id'));
    }

    /**
     * Mostrar formulario de edicion.
     */
    public function edit(string $id)
    {
        return view('hr::departamentos.edit', compact('id'));
    }

    /**
     * Actualizar departamento.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementar logica de actualizacion
        return redirect()->route('hr.configuracion.departamentos.index')
            ->with('success', __('Departamento actualizado exitosamente'));
    }

    /**
     * Eliminar departamento.
     */
    public function destroy(string $id)
    {
        // TODO: Implementar logica de eliminacion
        return redirect()->route('hr.configuracion.departamentos.index')
            ->with('success', __('Departamento eliminado exitosamente'));
    }
}
