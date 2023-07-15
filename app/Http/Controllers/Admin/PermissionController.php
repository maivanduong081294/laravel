<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Auth;
use Validator;
use DB;

use App\Models\User;
use App\Models\Permission;
use App\Models\Group;
use App\Models\Route;

class PermissionController extends Controller
{
    protected $view_prefix = 'admin.permissions.';
    private $homeRoute = 'admin.permissions';

    public function __construct() {
        $this->title = 'Phân quyền';
        $this->heading = 'Phân quyền';
    }

    public function index(Request $request)
    {
        $permission = new Permission();
        $user = new User();

        $actions = self::indexPageActions();
        $listStatus = self::getStatusList();
        $listHidden = self::getHiddenList();
        $listGroup = self::getGroupList();
        $listUser = $user->showTree();

        $where = [];
        $search = trim($request->search);
        if($search) {
            $where[] = [
                'permissions.name','like','%'.$search.'%',
            ];
        }

        if($request->has('group')) {
            $group_id = in_array($request->group,array_keys((array) $listGroup))?$request->group:0;
            if($group_id) {
                $where[] = [
                    'raw' => ['FIND_IN_SET(?, group_ids)', [$group_id]],
                ];
            }    
        }

        if($request->has('user')) {
            $user_id = in_array($request->user,array_keys((array) $listUser))?$request->user:0;
            if($user_id) {
                $userDetail = $user->getDetailById($user_id);
                if($userDetail) {
                    $sql = "FIND_IN_SET(?, user_ids)";
                    $sqlData = [$user_id];
                    $user_group_id = in_array($userDetail->group_id,array_keys((array) $listGroup))?$userDetail->group_id:0;
                    if(1 || $user_group_id) {
                        $sql.= " OR FIND_IN_SET(?, group_ids)";
                        $sqlData[] = $user_group_id;
                    }
                    $sql = "({$sql})";
                    $where[] = [
                        'raw' => [$sql, $sqlData],
                    ];
                }
            }    
        }

        if($request->has('status') && $request->status !== null) {
            $status = $request->status=='1'?1:0;
            $where[] = [
                'status'=>$status
            ];
        }

        if($request->has('hidden') && $request->hidden !== null) {
            $hidden = $request->hidden=='1'?1:0;
            $where[] = [
                'hidden',$hidden
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


        $list = $permission->getListIndex($where,$order);

        $perPage = $permission->getPerPage();

        return $this->view('index',compact('list','perPage','actions','listGroup','listStatus','listHidden','listUser'));
    }

    public function add(Request $request) {
        $permission = new Permission();

        if($request->isMethod('POST')) {
            if(!$request->has('group_ids')) {
                $request->request->add(['group_ids' => []]);
            }
            if(!$request->has('user_ids')) {
                $request->request->add(['user_ids' => []]);
            }
            
            $validator = self::validator($request);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('msg','Chỉnh sửa phân quyền không thành công');
            }
            $item = $permission->create($request->all());
            return redirect()->route($this->homeRoute.'.edit',['id'=>$item->id])->with(['msg'=>'Thêm phân quyền thành công','type'=>'success']);
        }
        $this->title = 'Thêm quyền truy cập';
        $this->heading = 'Thêm mới';
        $this->setBreadcrumb ([
            [
                'link' => route($this->homeRoute),
                'title' => 'Phân quyền'
            ],
        ]);
        $route = new Route();
        $user = new User();

        $whereRoute = [
            ['super_admin',0]
        ];
        $listRoute = $route->showTree([],$whereRoute);
        $listUser = $user->showTree();
        $listPermission = $permission->showTree();
        $listGroup = self::getGroupList();

        $selectedRoutes = $permission->getSelectedRouteIds();

        return $this->view('add',compact('listGroup','listRoute','listUser','listPermission','selectedRoutes'));
    }

    public function edit(Request $request) {
        $permission = new Permission();
        $itemid = $request->id;
        $detail = $permission->getDetailById($itemid);
        if(!$detail) {
            return redirect()->route($this->homeRoute)->with(['msg'=>'Phân quyền không tồn tại']);
        }

        if($request->isMethod('POST')) {
            if(!$request->has('group_ids')) {
                $request->request->add(['group_ids' => []]);
            }
            if(!$request->has('user_ids')) {
                $request->request->add(['user_ids' => []]);
            }
            if(!$request->has('parent_id')) {
                $request->request->add(['parent_id' => 0]);
            }
            
            $validator = self::validator($request);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('msg','Tạo phân quyền không thành công');
            }
            $item = $permission->updateById($itemid,$request->all());
            return back()->with(['msg'=>'Cập nhật thành công','type'=>'success']);
        }
        $this->title = 'Chỉnh sửa quyền truy cập';
        $this->heading = 'Chỉnh sửa';
        $this->setBreadcrumb ([
            [
                'link' => route($this->homeRoute),
                'title' => 'Phân quyền'
            ],
        ]);
        $route = new Route();
        $user = new User();

        $whereRoute = [
            ['super_admin',0]
        ];
        $listRoute = $route->showTree([],$whereRoute);
        $listUser = $user->showTree();
        $listPermission = $permission->showTree($detail->id);
        $listGroup = self::getGroupList();
        $selectedRoutes = $permission->getSelectedRouteIds($detail->route_id);

        return $this->view('edit',compact('detail','listGroup','listRoute','listUser','listPermission','selectedRoutes'));
    }

    public function menu() {
        $user = Auth::user();
        $menu = [];

        $beforeMenu = [
            [
                'name' => 'Bảng điều khiển',
                'link' => '/admin',
                'icon' => '<i class="fa-solid fa-house"></i>'
            ],
        ];
    
        $afterMenu = [];
        if(User::isAdminUser()) {
            $afterMenu[] = [
                'name' => 'Phân quyền',
                'link' => '/admin/permissions',
                'icon' => '<i class="fa-solid fa-arrows-to-eye"></i>',
                'children' => [
                    [
                        'name' => 'Danh sách',
                        'link' => '/admin/permissions',
                    ],
                    [
                        'name' => 'Thêm mới',
                        'link' => '/admin/permissions/add',
                    ]
                ]
            ];
        }
        if(User::isRootUser()) {
            $afterMenu[] = [
                'name' => 'Định tuyến',
                'link' => '/admin/routes',
                'icon' => '<i class="fa-solid fa-route"></i>',
                'children' => [
                    [
                        'name' => 'Danh sách',
                        'link' => '/admin/routes',
                    ],
                    [
                        'name' => 'Thêm mới',
                        'link' => '/admin/routes/add',
                    ]
                ]
            ];
        }
        return array_merge($beforeMenu,$menu,$afterMenu);
    }

    public function delete(Request $request) {
        $model = new Permission();
        if($request->has('id')) {
            $detail = $model->find($request->id);
            if($detail) {
                if($detail->super_admin == 1) {
                    abort(403,'Phân quyền này không thể xoá');
                }
                $model->deleteById($request->id);
                $model->flushCache();
                $request->session()->flash('msg','Xoá thành công');
                $request->session()->flash('type','success');
            }
            else {
                abort(404,'Phân quyền không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $where = [
                [
                    'super_admin',
                    '!=',
                    '1',
                ]
            ];
            $list = $model->getListByIds($ids,$where);
            if(empty($list)) {
                $all = $model->getListByIds($ids);
                if(!empty($all)) { 
                    abort(403,'Bạn không thể xoá những phân quyền đã chọn');
                }
                else {
                    abort(404,'Phân quyền không tồn tại');
                }
            }
            else {
                $model->deleteByIds($ids,$where);
                $model->flushCache();
                $request->session()->flash('msg','Xoá danh sách thành công');
                $request->session()->flash('type','success');
            }
        }
        else {
            abort(404);
        }
    }

    public function update(Request $request) {
        $model = new Permission();
        if($request->has('id')) {
            $route = $model->find($request->id);
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
                    $model->flushCache();
                }
            }
            else {
                abort(404,'Router không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $where = [
                [
                    'super_admin',
                    '!=',
                    '1',
                ]
            ];
            $list = $model->getListByIds($ids,$where);
            if(empty($list)) {
                $all = $model->getListByIds($ids);
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
                    $query = $model->whereIn('id',$ids);
                    $query = $model->setWhere($query,$where);
                    switch($action) {
                        case 'enabled_status':
                            $query->update(['status'=>1]);
                            break;
                        case 'disabled_status':
                            $query->update(['status'=>0]);
                            break;
                        case 'enabled_hidden':
                            $query->update(['hidden'=>1]);
                            break;
                        case 'disabled_hidden':
                            $query->update(['hidden'=>0]);
                            break;
                        default:
                            abort(404,'Hành động không tồn tại');
                    }
                    $request->session()->flash('msg','Cập nhật danh sách thành công');
                    $request->session()->flash('type','success');
                    $model->flushCache();
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

    private function indexPageActions() {
        return [
            'enabled_status' => 'Hoạt động',
            'disabled_status' => 'Không hoạt động',
            'disabled_hidden' => 'Hiển thị trên menu',
            'enabled_hidden' => 'Ẩn trên menu',
            'delete' => 'Xoá phân quyền',
        ];
    }
    private function getGroupList() {
        $group = new Group();
        $list = $group->showList();
        return $list;
    }

    private function getHiddenList() {
        return [
            0 => "Hiển thị",
            1 => "Ẩn",
        ];
    }

    private function getStatusList() {
        return [
            0 => "Không hoạt động",
            1 => "Hoạt động",
        ];
    }

    private function validator ($request) {
        $rules = [
            'name' => 'required',
            'route_id' => ['required',function($attribte,$value,$fail) use ($request) {
                $obj = new Permission();
                $route = new Route();
                $where = [
                    ['route_id',$value],
                ];
                if($request->id) {
                    $where[] = ['id','!=',$request->id];
                }
                $item = $obj->getDetail($where);
                if($item) {
                    return $fail('Quyền này đã được tạo rồi bạn có thể vào chỉnh sửa');
                }
                else {
                    $routeDetail = $route->getDetailById($value);
                    if(!$routeDetail) {
                        return $fail('Không tìm thấy quyền được chọn');
                    }
                }
            }],
            'group_ids' => [function($attribute,$value,$fail) use($request) {
                if(empty($request->group_ids) && empty($request->user_ids)) {
                    return $fail('Nhóm người dùng hoặc Tài khoản không thể để trống');
                }
                elseif(!empty($request->group_ids)) {
                    $group_ids = array_keys((array)self::getGroupList());
                    $checkList = array_intersect($request->group_ids,$group_ids);
                    if(empty($checkList)) {
                        return $fail('Nhóm người dùng không tồn tại');
                    }
                }
            }],
            'user_ids' =>[function($attribute,$value,$fail) use($request) {
                if(empty($request->group_ids) && empty($request->user_ids)) {
                    return $fail('Nhóm người dùng hoặc Tài khoản không thể để trống');
                }
                elseif(!empty($request->user_ids)) {
                    $user = new User();
                    $user_ids = array_keys((array) $user->showTree());
                    $checkList = array_intersect($request->user_ids,$user_ids);
                    if(empty($checkList)) {
                        return $fail('Tài khoản không tồn tại');
                    }
                }
            }],
            'parent_id' => [function($attribute,$value,$fail) use($request) {
                $value = (int) $value;
                if($value > 0) {
                    $obj = new Permission();
                    $item = $obj->getDetailById($value);
                    if(!$item) {
                        return $fail("Phân quyền cha không tồn tại");
                    }
                    if($request->id) {
                        $children = $obj->getChildrenIds($request->id);
                        if(!empty($children) && in_array($value,$children)) {
                            return $fail("Không thể chọn phân quyền con của nó");
                        }
                    }
                }
            }],
        ];

        $message = [
            'required' => ':attribute không thể để trống'
        ];

        $attributes = [
            'name' => 'Tên phân quyền',
            'route_id' => 'Quyền truy cập',
        ];

        $validator = Validator::make($request->all(),$rules,$message,$attributes);
        return $validator;
    }
}
