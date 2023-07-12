<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Route extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title','as','uri', 'controller', 'function', 'middleware','status','hidden','super_admin','name','parent_id','method'
    ];

    protected $attribtes = [
        "status" => 1,
        "super_admin" => 0,
        "parent_id" => 0,
        "hidden" => 0,
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

    public function filterData($data) {
        if(isset($data['method'])) {
            $methods = routeMethods();
            $method = $data['method'];
            if(!is_array($method) && trim($method)) {
                $method = explode(',',$method);
            }
            if(is_array($method)) {
                $method = array_intersect($method,$methods);
                if(!empty($method)) {
                    $method = implode(',',$method);
                }
                else {
                    $method = '';
                }
            }
            else {
                $method = '';
            }
            $data['method'] = $method;
        }
        if(isset($data['parent_id']) && $data['parent_id'] > 0) {
            $parentDetail = self::getDetailById($data['parent_id']);
            if($parentDetail) {
                if($data['function'] == 'index') {
                    $data['name'] = str_replace($parentDetail->function,$data['function'],$parentDetail->name);
                }
                else {
                    $data['name'] = $parentDetail->name.'.'.$data['function'];
                }
            }
        }
        else {
            $data['name'] = 'admin.'.strtolower($data['controller']);
        }
        return parent::filterData($data);
    }
}
