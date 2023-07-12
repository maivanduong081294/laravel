<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

use Auth;
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
        //

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

    public function add() {
        $this->title = 'Thêm quyền truy cập';
        $this->heading = 'Thêm mới';
        $this->setBreadcrumb ([
            [
                'link' => route($this->homeRoute),
                'title' => 'Phân quyền'
            ],
        ]);

        $permission = new Permission();
        $route = new Route();
        $user = new User();

        $whereRoute = [
            ['super_admin',0]
        ];
        $listRoute = $route->showTree([],$whereRoute);
        $listUser = $user->showTree();
        $listPermission = $permission->showTree();
        $listStatus = self::getStatusList();
        $listHidden = self::getHiddenList();
        $listGroup = self::getGroupList();

        return $this->view('add',compact('listGroup','listStatus','listHidden','listRoute','listUser','listPermission'));
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
}
