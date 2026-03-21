<?php

namespace Modules\ERP\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class OrdenTrabajoController extends BaseController
{
    public function index(Request $request): View
    {
        return view('erp::ordenes_trabajo.index');
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (!Schema::hasTable('erp_ordenes_trabajo')) {
            return redirect()->to(group_route('erp.work-orders.index'))
                ->with('status', 'La tabla erp_ordenes_trabajo aun no existe. Ejecuta migraciones primero.');
        }

        return view('erp::ordenes_trabajo.create');
    }
}
