<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    //
    protected $view_prefix = 'admin.general.';

    protected $title = 'Bảng điều khiển';
    protected $heading = 'Bảng điều khiển';

    public function index(Request $request) {
        //$action = $request->route()->getAction();
        //$this->setBreadcrumb();
        return $this->view('dashboard');
    }

}
