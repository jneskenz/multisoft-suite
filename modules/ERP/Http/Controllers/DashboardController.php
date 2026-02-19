<?php

namespace Modules\ERP\Http\Controllers;

class DashboardController extends BaseController
{
    /**
     * Mostrar dashboard de ERP.
     */
    public function index()
    {
        return view('erp::index');
    }
}
