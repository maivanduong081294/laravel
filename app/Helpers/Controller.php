<?php
$tag = '';
function listAdminController($name = false) {
    $controllers = [
        'General' => \App\Http\Controllers\Admin\GeneralController::class,
        'Permissions' => \App\Http\Controllers\Admin\PermissionController::class,
        'Users' => \App\Http\Controllers\Admin\UserController::class,
        'Routes' => \App\Http\Controllers\Admin\RouteController::class,
        'Upload' => \App\Http\Controllers\Admin\UploadController::class,
        'TinyPNG' => \App\Http\Controllers\Admin\TinyPNGController::class,
    ];
    if($name) {
        return array_keys($controllers);
    }
    return $controllers;
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
?>