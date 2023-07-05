<?php

use App\Models\Route as RouteModel;
use Illuminate\Support\Facades\Schema;

function getAdminRoute() {
    if(Schema::hasTable('routes')) {
        $route = new RouteModel();
        $routes = $route->getTreeRoutes();
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
            Route::prefix($prefix)->name($name.'.')->group(function() use($route) {
                $route->uri = '/';
                setRoute($route);
                foreach($route->children as $chilren) {
                    includeRoute($chilren);
                }
            });
        }
        else {
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
                $set = Route::get($route->uri,[$controller,$route->function])->name($route->function);
            }
            elseif(in_array('any',$methods)) {
                $set = Route::any($route->uri,[$controller,$route->function])->name($route->function);
            }
            else {
                $set = Route::match($methods,$route->uri,[$controller,$route->function])->name($route->function);
            }
            if($route->middleware) {
                $set->middleware($route->middleware);
            }
        }
    }
}