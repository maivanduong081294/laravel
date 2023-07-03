<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $view_prefix = '';
    protected $title = 'Laptop Now';

    protected function view($view,$data=[],$mergeData=[]) {
        if($this->view_prefix) {
            $view = $this->view_prefix.$view;
        }
        $defaultData =  [
            'title' => $this->title
        ];
        $data = array_merge($defaultData,$data);
        return view($view,$data,$mergeData);
    }
}
