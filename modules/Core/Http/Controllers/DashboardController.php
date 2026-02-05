<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Mostrar el dashboard principal.
     */
    public function index()
    {
        return view('core::dashboard');
    }
}
