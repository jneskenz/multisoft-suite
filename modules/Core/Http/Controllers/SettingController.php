<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends BaseController
{
    /**
     * Mostrar configuraciones.
     */
    public function index()
    {
        return view('core::settings.index');
    }

    /**
     * Actualizar configuraciones.
     */
    public function update(Request $request)
    {
        // TODO: Implementar lógica de actualización
        return redirect()->route('core.settings.index')
            ->with('success', __('Configuración actualizada exitosamente'));
    }
}
