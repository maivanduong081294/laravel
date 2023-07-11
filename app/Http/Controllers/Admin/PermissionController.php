<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Request;

use Auth;

use App\Models\User;
use App\Models\Permission;

class PermissionController extends Controller
{
    protected $view_prefix = 'admin.permissions.';
    private $homeRoute = 'admin.permissions';

    public function __construct() {
        $this->title = 'Phân quyền';
        $this->heading = 'Phân quyền';
    }

    public function index()
    {
        //
        $permission = new Permission();
        $list = $permission->getAllList();
        $perPage = $permission->getPerPage();
        return $this->view('index',compact('list','perPage'));
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
        return $this->view('add');
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
}
