<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends BaseModel
{
    use HasFactory;

    public function getList(array $where = [], array $orderBy = []) {
        // $where = array_merge([
        //     [
        //         'super_admin',
        //         '!=',
        //         '1',
        //     ]
        // ],$where);
        return parent::getList($where,$orderBy);
    }

    public function getRoutes() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::where('status',1);
            $result = $query->orderby('controller')->get();
            if($result) {
                $routes = [];
                foreach($result as $route) {
                    $routes[$route->controller][$route->function] = $route;
                }
                $value = $routes;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
