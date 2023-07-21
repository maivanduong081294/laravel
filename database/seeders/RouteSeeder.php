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
            'super_admin' => 1,
            'method' => 'GET',
            'hidden' => 1,
        ];

        $data['TinyPNG'] = [
            'title' => 'Tiny PNG API',
            'uri' => '/tinypng',
            'controller' => 'TinyPNG',
            'initChildren' => 1,
            'function' => 'index',
            'method' => 'GET',
        ];

        $data['Upload'] = [
            'title' => 'Thư viện ảnh',
            'uri' => '/upload',
            'controller' => 'Upload',
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

        $initChildren = [];
        if((!isset($data['parent_id']) || $data['parent_id']==0) && isset($data['initChildren']) && $data['initChildren']> 0) {
            $initChildren = [
                'add' => [
                    'title' => 'Thêm '.$data['title'],
                    'uri' => '/add',
                    'function' => 'add',
                    'method' => 'POST,GET',
                ],
                'edit' => [
                    'title' => 'Sửa '.$data['title'],
                    'uri' => '/edit/{id}',
                    'function' => 'edit',
                    'method' => 'POST,GET',
                ],
                'update' => [
                    'title' => 'Cập nhật '.$data['title'],
                    'uri' => '/update',
                    'function' => 'update',
                    'method' => 'POST',
                ],
                'delete' => [
                    'title' => 'Xoá '.$data['title'],
                    'uri' => '/delete',
                    'function' => 'delete',
                    'method' => 'DELETE',
                ],
            ];
            if(in_array($controller,['TinyPNG','Upload'])) {
                $initChildren['add']['method'] = 'POST';
                $initChildren['edit']['method'] = 'POST';
            }
        }
        $children = !empty($data['children'])?array_merge($initChildren,$data['children']):$initChildren;
       

        $item = $route->where('uri',$uri)->where('controller',$controller)->where('function',$function)->where('method',$method)->first();
        
        if(!$item) {
            unset($data['initChildren']);
            $item = $route->create($data);
            $item = $route->find($item->id);
        }
        if($item && !empty($children)) {
            foreach($children as $routeChild) {
                if(!isset($routeChild['controller'])) {
                    $routeChild['controller'] = $item->controller;
                }
                if(!isset($routeChild['hidden'])) {
                    $routeChild['hidden'] = $item->hidden;
                }
                if(!isset($routeChild['super_admin'])) {
                    $routeChild['super_admin'] = $item->super_admin;
                }
                $routeChild['parent_id'] = $item->id;
                self::insertData($routeChild);
            }
        }
    }
}
