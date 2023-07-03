<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    //
    protected $view_prefix = 'admin.routes.';
    public $title = 'Routes';
    public function index() {
        $this->title = 'Danh sách định tuyến';
        $this->heading = 'Danh sách định tuyến';
        return $this->view('index');
    }

    public function add() {
        return 'add';
    }

    public function edit(Request $request) {
        echo $request->id;
        return 'edit';
    }
}
