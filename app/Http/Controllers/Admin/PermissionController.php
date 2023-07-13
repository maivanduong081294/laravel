<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

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
        $actions = self::indexPageActions();
        $listStatus = self::getStatusList();
        $listHidden = self::getHiddenList();
        $listGroup = self::getGroupList();

        $permission = new Permission();

        $where = [];
        $search = trim($request->search);
        if($search) {
            $where[] = [
                'name','like','%'.$search.'%',
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

        if($request->has('status')) {
            $status = $request->status=='1'?1:0;
            $where[] = [
                'status'=>$status
            ];
        }

        if($request->has('hidden')) {
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

        $list = $permission->getList($where,$order);

        $perPage = $permission->getPerPage();

        return $this->view('index',compact('list','perPage','actions','listGroup','listStatus','listHidden'));
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

        return $this->view('add',compact('listGroup','listRoute','listUser','listPermission'));
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

        return $this->view('edit',compact('detail','listGroup','listRoute','listUser','listPermission'));
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

    public function delete() {

    }

    private function indexPageActions() {
        return [
            'enabled_status' => 'Hoạt động',
            'disabled_status' => 'Không hoạt động',
            'disabled_hidden' => 'Thêm vào menu',
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
                        return $fail("Quyền phụ thuộc không tồn tại");
                    }
                    if($request->id) {
                        $children = $obj->getChildrenIds($request->id);
                        if(!empty($children) && in_array($value,$children)) {
                            return $fail("Không thể chọn phân quyền con để phụ thuộc");
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
