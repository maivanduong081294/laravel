<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

use App\Models\Route;
use DB;

class RouteController extends Controller
{
    //
    protected $view_prefix = 'admin.routes.';
    public $title = 'Routes';

    private function indexPageActions() {
        return [
            'delete' => 'Xoá định tuyến',
        ];
    }
    public function index(Request $request) {
        $this->title = 'Danh sách định tuyến';
        $this->heading = 'Danh sách định tuyến';
        $where = [];
        $search = trim($request->search);
        if($search) {
            $where[] = [
                [
                    'title','like','%'.$search.'%'
                ],
                [
                    'middleware','like','%'.$search.'%'
                ],
                [
                    'function','like','%'.$search.'%'
                ],
            ];
        }
        $controller = trim($request->controller);
        if($controller) {
            $where[] = [
                [
                    'controller','=',$controller
                ],
            ];
        }
        $method = trim($request->getMethod);
        if($method) {
            $where[] = [
                [
                    'method','like','%'.$method.'%'
                ],
            ];
        }

        $route = new Route();

        $list = $route->getList($where);

        $actions = self::indexPageActions();
        $controllers = Route::getControllers();
        $methods = routeMethods();
        $status = [
            0 => 'Tất cả trạng thái',
            1 => 'Hoạt động',
            2 => 'Không hoạt động',
            3 => 'Ẩn phân quyền',
        ];

        $perPage = $route->getPerPage();

        return $this->view('index',compact('list','actions','controllers','methods','status','perPage'));
    }

    public function add() {
        return 'add';
    }

    public function edit(Request $request) {
        echo $request->id;
        return 'edit';
    }
}
