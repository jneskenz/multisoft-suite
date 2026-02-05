<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Mostrar dashboard de HR.
     */
    public function index()
    {
        return view('hr::dashboard');
    }
}
