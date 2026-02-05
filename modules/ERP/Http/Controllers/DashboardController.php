<?php

namespace Modules\ERP\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Mostrar dashboard de ERP.
     */
    public function index()
    {
        return view('erp::dashboard');
    }
}
