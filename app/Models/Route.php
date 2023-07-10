<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title','as','uri', 'controller', 'function', 'middleware','status','hidden','super_admin','name','parent_id',
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
            $order = [
                [
                    'orderBy' => 'title',
                    'orderType' => 'ASC',
                ]
            ];
            $routes = parent::getAllList($where,$order);
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
            $order = [
                [
                    'orderBy' => 'title',
                    'orderType' => 'ASC',
                ]
            ];
            $routes = parent::getAllList($where,$order);
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

    public function showTreeRoute($ids=[], $parents=[], $parent_route = [],$prefix='') {
        if(empty($parent_route)) {
            $routes = Route::getTreeRoutes();
        }
        else {
            $routes = null;
            $route = $parent_route;
        }
        if(is_array($routes)) {
            foreach($routes as $route) {
                if(((is_array($ids) && in_array($route->id,$ids)) || $ids == $route->id)) {
                    continue;
                }
                $parents[$route->id] = $route->title;
                if(!empty($route->children)) {
                    foreach($route->children as $child) {
                        $parents = self::showTreeRoute($ids, $parents, $child);
                    }
                }
            }
        }
        else {
            if(((is_array($ids) && in_array($route->id,$ids)) || $ids == $route->id)) {
                return $parents;
            }
            $prefix=$prefix.'---';
            $parents[$route->id] = $prefix.' '.$route->title;
            if($route->children) {
                foreach($route->children as $child) {
                    $parents = self::showTreeRoute($ids, $parents, $child, $prefix);
                }
            }
        }
        return $parents;
    }

    public function create($data) {
        if(isset($data['parent_id']) && $data['parent_id'] > 0) {
            $parentDetail = self::getDetailById($data['parent_id']);
            if($parentDetail) {
                if($parentDetail->function == 'index') {
                    $data['name'] = str_replace($parentDetail->function,$data['function'],$parentDetail->name);
                }
                else {
                    $data['name'] = $parentDetail->name.'.'.$data['function'];
                }
            }
        }
        else {
            $data['name'] = 'admin.'.$data['function'];
        }
        $result = parent::create($data);
        self::flushCache();
        return $result;
    }
}
