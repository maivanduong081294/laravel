<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    //
    protected $title = 'Admin';
    protected $heading = 'Admin';
    protected $breadcrumb = [];

    protected function setBreadcrumb($links=[]) {
        $this->breadcrumb = array_merge($links,[
            ['title' => $this->heading],
        ]);
    }

    protected function view($view,$data=[],$mergeData=[]) {
        $defaultData =  [
            'heading' => $this->heading,
            'breadcrumb' => $this->breadcrumb,
        ];
        $data = array_merge($defaultData,$data);
        return parent::view($view,$data,$mergeData);
    }
}
