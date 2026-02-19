<?php

namespace Modules\HR\Http\Controllers;

class CargoController extends BaseController
{
    /**
     * Listar cargos.
     */
    public function index()
    {
        return view('hr::cargos.index');
    }

    /**
     * Mostrar cargo.
     */
    public function show(string $id)
    {
        return view('hr::cargos.show', compact('id'));
    }
}
