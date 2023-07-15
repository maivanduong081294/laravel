<?php

use App\Models\Route as RouteModel;
use Illuminate\Support\Facades\Schema;

function getAdminRoute() {
    if(Schema::hasTable('routes')) {
        $route = new RouteModel();
        $routes = $route->getTree();
        if(!empty($routes)) {
            foreach($routes as $route) {
                includeRoute($route);
            }
        }
    }
}

function includeRoute($route) {
    if($route) {
        if($route->children) {
            $name = trim(strtolower($route->controller));
            $prefix = $route->uri;
            if(strpos($prefix,'/')===0) {
                $prefix = substr($prefix,1);
            }
            $name = str_replace('/','.',$prefix);
            $route->slug = $prefix;
            setRoute($route);
            Route::prefix($prefix)->name($name.'.')->group(function() use($route) {
                foreach($route->children as $chilren) {
                    includeRoute($chilren);
                }
            });
        }
        else {
            $route->slug = $route->function;
            setRoute($route);
        }
    }
}
function setRoute($route) {
    if($route) {
        $controller = getAdminController($route->controller,false);
        if($controller) {
            $methods = explode(',',$route->method);
            if(!$route->method) {
                $set = Route::get($route->uri,[$controller,$route->function]);
            }
            elseif(in_array('any',$methods)) {
                $set = Route::any($route->uri,[$controller,$route->function]);
            }
            else {
                $set = Route::match($methods,$route->uri,[$controller,$route->function]);
            }
            $set = $set->name($route->slug);
            if($route->middleware) {
                $set->middleware($route->middleware);
            }
        }
    }
}

function routeMethods() {
    return [
        "GET","POST","PUT","DELETE",
    ];
}

function getRouteByName($name) {
    $routes = Route::getRoutes();
    return $routes->getByName($name);
}

function getLinkByRouteName($name,string $method="GET") {
    $method = strtoupper($method);
    if(!in_array($method, routeMethods())) {
        return "";
    }
    $route = getRouteByName($name);
    $link = '';
    if($route) {
        preg_match_all('/{(.*?)}/',  $route->uri, $matchedMissingParameters);
        if(empty($matchedMissingParameters[0])) {
            if(in_array($method,$route->methods())) {
                return route($name);
            }
        }
    }
    return $link;
}