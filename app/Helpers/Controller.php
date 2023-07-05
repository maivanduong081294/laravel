<?php
$tag = '';
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
?>