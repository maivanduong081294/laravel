<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

use Validator;

use App\Models\Route;

class RouteController extends Controller
{
    //
    protected $view_prefix = 'admin.routes.';
    private $homeRoute = 'admin.routes';

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

    public function add(Request $request) {
        $route = new Route();
        if($request->isMethod('POST')) {
            $validator = self::validator($request);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with(['msg' => 'Thêm định tuyến không thành công']);
            }
            $data = $request->all();
            $item = $route->create($request->all());
            return redirect()->route($this->homeRoute.'.edit',['id'=>$item->id])->with(['msg'=>'Thêm định tuyến thành công','type'=>'success']);
        }
        $this->title = 'Thêm định tuyến';
        $this->heading = 'Thêm định tuyến';
        $breadcrumbs = [
            [
                'link' => route($this->homeRoute),
                'title' => 'Định tuyến'
            ],
        ];
        self::setBreadcrumb($breadcrumbs);

        $treeRoute = $route->showTree();

        return $this->view('add',compact('treeRoute'));
    }

    public function edit(Request $request) {
        $route = new Route();
        $itemid = $request->id;
        $detail = $route->getDetailById($itemid);
        if(!$detail) {
            return redirect()->route($this->homeRoute)->with(['msg'=>'Định tuyến không tồn tại']);
        }

        if($request->isMethod('POST')) {
            $validator = self::validator($request);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with(['msg'=>'Cập nhật không thành công']);
            }
            $result = $route->updateById($request->id,$request->all());
            if($result) {
                return back()->with(['msg'=>'Cập nhật thành công','type'=>'success']);
            }
            else {
                return back()->withErrors($validator)->withInput()->with(['msg'=>'Cập nhật không thành công']);
            }
        }

        $this->title = 'Chỉnh sửa định tuyến';
        $this->heading = 'Chỉnh sửa định tuyến';
        $breadcrumbs = [
            [
                'link' => route($this->homeRoute),
                'title' => 'Định tuyến'
            ],
        ];
        self::setBreadcrumb($breadcrumbs);

        $treeRoute = $route->showTree($detail->id);

        return $this->view('edit',compact('treeRoute','detail'));
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
                $request->session()->flash('msg','Xoá thành công');
                $request->session()->flash('type','success');
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
                $request->session()->flash('msg','Xoá danh sách thành công');
                $request->session()->flash('type','success');
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
                    $request->session()->flash('msg','Cập nhật thành công');
                    $request->session()->flash('type','success');
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
                        default:
                            abort(404,'Hành động không tồn tại');
                    }
                    $request->session()->flash('msg','Cập nhật danh sách thành công');
                    $request->session()->flash('type','success');
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

    function validator($request) {
        $rules = [
            'title' => 'required',
            'uri' => 'required',
            'controller' => ['required', function($attribte,$value,$fail) {
                if(!in_array($value,listAdminController(true))) {
                    $fail("Controller không tồn tại");
                }
            }],
            'function' => ['required', function($attribte,$value,$fail) use ($request){
                if($request->controller) {
                    $controller = getAdminController($request->controller);
                    if(!$controller) {
                        return $fail("Function không tồn tại");
                    }
                    if(!method_exists($controller,$value)) {
                        return $fail("Function không tồn tại");
                    }
                }
                else {
                    return $fail("Chưa chọn Controller");
                }
            }],
            'method' => ['required', function($attribte,$value,$fail) {
                $methods = routeMethods();
                if(is_array($value)) {
                    if(empty(array_filter($value))) {
                        return $fail("Method không thể để trống");
                    }
                    $values = array_intersect($value,$methods);
                    if(empty($values)) {
                        return $fail("Method không tồn tại");
                    }
                }
                elseif(!in_array($value,$methods)) {
                    return $fail("Method không tồn tại");
                }
            }],
            'parent_id' => [function($attribute,$value,$fail) use($request) {
                $value = (int) $value;
                if($value > 0) {
                    $route = Route::getDetailById($value);
                    if(!$route) {
                        return $fail("Định tuyến không tồn tại");
                    }
                    if($request->id) {
                        $route = new Route();
                        $children = $route->getChildrenIds($request->id);
                        if(!empty($children) && in_array($value,$children)) {
                            return $fail("Không thể chọn định tuyến con để phụ thuộc");
                        }
                    }
                }
            }],
        ];

        $messages = [
            'required' => ':attribute không thể để trống'
        ];

        $attributes = [
            'title' => 'Tên định tuyến',
            'uri' => 'Đường dẫn',
            'controller' => 'Controller',
            'function' => 'Function',
            'method' => 'Method',
        ];

        $validator = Validator::make($request->all(),$rules,$messages,$attributes);
        return $validator;
    }
}
