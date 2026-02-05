<?php

namespace Modules\ERP\Http\Controllers;

use Illuminate\Http\Request;

class ContextController extends BaseController
{
    /**
     * Seleccionar contexto de empresa.
     */
    public function select()
    {
        return view('erp::context.select');
    }

    /**
     * Establecer contexto de empresa.
     */
    public function set(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        session(['erp.company_id' => $request->company_id]);

        return redirect()->route('erp.dashboard')
            ->with('success', __('Contexto de empresa establecido'));
    }
}
