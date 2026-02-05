<?php

namespace Modules\FMS\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Mostrar dashboard de FMS.
     */
    public function index()
    {
        return view('fms::dashboard');
    }
}
