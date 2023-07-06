<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AjaxController extends Controller
{
    //

    public function index(Request $request) {
        $action = $request->action;
        if($action && method_exists($this,$action)) {
            $respone = [$this,$action]($request);
            return $respone;
        }
        return abort(404);
    }

    private function setShowItemsNumber($request) {
        $number = setShowItemsNumber($request->number);
        $request->session()->put('perPage', $number);
    }
}
