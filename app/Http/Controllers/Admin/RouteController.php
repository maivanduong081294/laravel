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
            'enabled_status' => 'Hoạt động',
            'disabled_status' => 'Không hoạt động',
            'enabled_permission' => 'Bật phân quyền',
            'disabled_permission' => 'Tắt phân quyền',
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
        $order = [];
        $orderBy = trim($request->orderBy);
        $orderType = trim($request->orderType);
        if($orderBy) {
            $orderType = $orderType == 'desc'?'desc':'asc';
            $order[] = [
                'orderBy' => $orderBy,
                'orderType' => $orderType
            ];
        }

        $route = new Route();

        $list = $route->getList($where,$order);

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
        $this->title = 'Thêm định tuyến';
        $this->heading = 'Thêm định tuyến';
        $breadcrumbs = [
            [
                'title' => 'Định tuyến',
                'link' => route('admin.routes.index'),
            ]
        ];
        self::setBreadcrumb($breadcrumbs);
        return $this->view('add');
    }

    public function edit(Request $request) {
        $this->title = 'Chỉnh sửa định tuyến';
        $this->heading = 'Chỉnh sửa định tuyến';
        $breadcrumbs = [
            [
                'title' => 'Định tuyến',
                'link' => route('admin.routes.index'),
            ]
        ];
        self::setBreadcrumb($breadcrumbs);
        return $this->view('add');
    }

    public function delete(Request $request) {
        if($request->has('id')) {
            $route = Route::find($request->id);
            if($route) {
                if($route->super_admin == 1) {
                    abort(403,'Router này không thể xoá');
                }
                Route::deleteById($request->id);
                Route::flushCache();
            }
            else {
                abort(404,'Router không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $route = new Route();
            $where = [
                [
                    'super_admin',
                    '!=',
                    '1',
                ]
            ];
            $list = $route->getListByIds($ids,$where);
            if(empty($list)) {
                $all = $route->getListByIds($ids);
                if(!empty($all)) { 
                    abort(403,'Bạn không thể xoá những Router đã chọn');
                }
                else {
                    abort(404,'Routers không tồn tại');
                }
            }
            else {
                $route->deleteByIds($ids,$where);
            }
        }
        else {
            abort(404);
        }
    }

    public function update(Request $request) {
        if($request->has('id')) {
            $route = Route::find($request->id);
            if($route) {
                if($route->super_admin == 1) {
                    abort(403,'Router này không thể cập nhật');
                }
                $updated = false;
                if($request->has('hidden')) {
                    $route->hidden = $request->hidden;
                    $updated = true;
                }
                if($request->has('status')) {
                    $route->status = $request->status;
                    $updated = true;
                }
                if($updated) {
                    $route->save();
                    Route::flushCache();
                }
            }
            else {
                abort(404,'Router không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $route = new Route();

            $where = [
                [
                    'super_admin',
                    '!=',
                    '1',
                ]
            ];
            $list = $route->getListByIds($ids,$where);
            if(empty($list)) {
                $all = $route->getListByIds($ids);
                if(!empty($all)) { 
                    abort(403,'Bạn không thể cập nhật những Router đã chọn');
                }
                else {
                    abort(404,'Routers không tồn tại');
                }
            }
            else {
                if($request->has('action')) {
                    $action = $request->action;
                    DB::enableQueryLog();
                    $query = Route::whereIn('id',$ids);
                    $query = Route::setWhere($query,$where);
                    switch($action) {
                        case 'enabled_status':
                            $query->update(['status'=>1]);
                            break;
                        case 'disabled_status':
                            $query->update(['status'=>0]);
                            break;
                        case 'enabled_permission':
                            $query->update(['hidden'=>0]);
                            break;
                        case 'disabled_permission':
                            $query->update(['hidden'=>1]);
                            break;
                        case 'delete':
                            self::delete($request);
                            break;
                        default:
                            abort(404,'Hành động không tồn tại');
                    }
                    Route::flushCache();
                }
                else {
                    abort(404,'Vui lòng chọn hành động');
                }
            }
        }
        else {
            abort(404);
        }
    }
}
