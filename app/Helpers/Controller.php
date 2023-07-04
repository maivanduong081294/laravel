<?php
$tag = '';
use App\Models\Route;
function listAdminController() {
    return [
        'General' => \App\Http\Controllers\Admin\GeneralController::class,
        'Permissions' => \App\Http\Controllers\Admin\PermissionController::class,
        'Users' => \App\Http\Controllers\Admin\UserController::class,
        'Routes' => \App\Http\Controllers\Admin\RouteController::class,
    ];
}

function getAdminController($controller,$callClass=true) {
    $controllers = listAdminController();
    if(!empty($controllers[$controller])) {
        if($callClass) {
            return new $controllers[$controller];
        }
        else {
            return $controllers[$controller];
        }
    }
    return false;
}

function listRouteControler() {
    $routes = Route::getRoutes();
    if(!empty($routes)) {
        return $routes = json_decode(json_encode($routes));
    }
    return false;
}
?>