<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

use App\Models\Route;

class RouteController extends Controller
{
    //
    protected $view_prefix = 'admin.routes.';
    public $title = 'Routes';
    public function index() {
        $this->title = 'Danh sách định tuyến';
        $this->heading = 'Danh sách định tuyến';
        $route = new Route();
        $route->setPerPage(1);
        $list = $route->getList();
        return $this->view('index',compact('list'));
    }

    public function add() {
        return 'add';
    }

    public function edit(Request $request) {
        echo $request->id;
        return 'edit';
    }
}
