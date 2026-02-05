<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Mostrar dashboard de Reports.
     */
    public function index()
    {
        return view('reports::dashboard');
    }
}
