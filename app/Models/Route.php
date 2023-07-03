<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends BaseModel
{
    use HasFactory;

    public function getRoutes() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(1 || !self::hasCache($keyCache)) {
            $query = self::where('status',1);
            $result = $query->get();
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
