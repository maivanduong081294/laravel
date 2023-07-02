<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    //
    protected $view_prefix = 'admin.general.';

    public function index(Request $request) {
        $action = $request->route()->getAction();
        return $this->view('dashboard');
    }
}
