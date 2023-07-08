<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title','as','uri', 'controller', 'function', 'middleware','status','hidden','super_admin',
    ];

    protected $attribtes = [
        "status" => 1,
        "super_admin" => 0,
    ];

    public function getList(array $where = [], array $orderBy = []) {
        if(!User::isRootUser()) {
            $where = array_merge([
                [
                    'super_admin',
                    '!=',
                    '1',
                ]
            ],$where);
        }
        return parent::getList($where,$orderBy);
    }

    public function getTreeRoutes() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = [
                ['status',1],
                ['parent_id',0]
            ];
            $routes = parent::getAllList($where);
            if($routes) {
                $newRoutes = [];
                foreach($routes as $route) {
                    $route->children = self::getChildrenRoutes($route->id);
                    $newRoutes[] = $route;
                }
                $value = $newRoutes;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getChildrenRoutes($id) {
        $keyCache = __FUNCTION__.'-'.$id;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = [
                ['status',1],
                ['parent_id',$id]
            ];
            $routes = parent::getAllList($where);
            if($routes) {
                $newRoutes = [];
                foreach($routes as $route) {
                    $route->children = self::getChildrenRoutes($route->id);
                    $newRoutes[] = $route;
                }
                $value = $newRoutes;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getControllers() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $result = parent::selectRaw("distinct(controller) as controller")->get();
            if($result) {
                $value = [];
                foreach($result as $item) {
                    $value[] = $item->controller;
                }
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getRouteByName($name) {
        $keyCache = __FUNCTION__.$name;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $value = parent::where('name',$name)->first();
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
