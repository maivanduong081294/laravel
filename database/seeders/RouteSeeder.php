<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use DB;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [];

        $data['Routes'] = [
            'title' => 'Định tuyến',
            'uri' => '/routes',
            'controller' => 'Routes',
            'function' => 'index',
            'initChildren' => 1,
            'method' => 'GET',
            'super_admin' => 1,
            'hidden' => 1,
        ];

        $data['Permissions'] = [
            'title' => 'Phân quyền',
            'uri' => '/permissions',
            'controller' => 'Permissions',
            'initChildren' => 1,
            'function' => 'index',
            'method' => 'GET',
        ];

        foreach($data as $routes) {
            self::insertData($routes);
        }

        Route::flushCache();
    }

    private function insertData($data) {
        $route = new Route();
        
        $uri = $data['uri'];
        $controller = $data['controller'];
        $function = $data['function'];
        $method = isset($data['method'])?$data['method']:'GET';
        if(isset($data['hidden'])) {
            $hidden = $data['hidden']==1?1:0;
        }
        if(isset($data['super_admin'])) {
            $super_admin = $data['super_admin']==1?1:0;
        }
        if(isset($data['parent_id'])) {
            $parent_id = $data['parent_id'] > 0 ?$data['parent_id']:0;
            $parentDetail = $route->where('id',$parent_id)->first();
            if(!$parentDetail) {
                $parent_id = 0;
            }
        }

        $initChildren = [];
        if((!isset($data['parent_id']) || $data['parent_id']==0) && isset($data['initChildren']) && $data['initChildren']> 0) {
            $initChildren = [
                [
                    'title' => 'Thêm '.$data['title'],
                    'uri' => '/add',
                    'function' => 'add',
                    'method' => 'POST,GET',
                ],
                [
                    'title' => 'Sửa '.$data['title'],
                    'uri' => '/edit/{id}',
                    'function' => 'edit',
                    'method' => 'POST,GET',
                ],
                [
                    'title' => 'Cập nhật '.$data['title'],
                    'uri' => '/update',
                    'function' => 'update',
                    'method' => 'POST',
                ],
                [
                    'title' => 'Xoá '.$data['title'],
                    'uri' => '/delete',
                    'function' => 'delete',
                    'method' => 'DELETE',
                ],
            ];
        }
        $children = !empty($data['children'])?array_merge($initChildren,$data['children']):$initChildren;

        if(!empty($children)) {
            $name = "admin.".strtolower($controller).".{$function}";
        }
        elseif(isset($parent_id) && $parent_id > 0) {
            $name = str_replace($parentDetail->function,$function,$parentDetail->name);
        }
        else {
            $name = "admin.{$function}";
        }

        $item = $route->where('uri',$uri)->where('controller',$controller)->where('function',$function)->where('method',$method)->first();
        
        if(!$item) {
            $route->title = $data['title'];
            $route->name = $name;
            $route->uri = $uri;
            $route->controller =  $controller;
            $route->function = $function;
            $route->method = $method;
            if(isset($hidden)) {
                $route->hidden = $hidden;
            }
            if(isset($super_admin)) {
                $route->super_admin = $super_admin;
            }
            if(isset($parent_id)) {
                $route->parent_id = $parent_id;
            }
            $route->save();
            $itemid = $route->id;
        }
        else {
            $itemid = $item->id;
        }
        if(!empty($children)) {
            foreach($children as $routeChild) {
                if(!isset($routeChild['controller'])) {
                    $routeChild['controller'] = $controller;
                }
                if(isset($hidden) && !isset($routeChild['hidden'])) {
                    $routeChild['hidden'] = $hidden;
                }
                if(isset($super_admin) && !isset($routeChild['super_admin'])) {
                    $routeChild['super_admin'] = $super_admin;
                }
                $routeChild['parent_id'] = $itemid;
                self::insertData($routeChild);
            }
        }
    }
}
