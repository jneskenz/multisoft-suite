<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Mostrar dashboard de CRM.
     */
    public function index()
    {
        return view('crm::dashboard');
    }
}
