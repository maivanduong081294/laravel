<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $onCache = true;

    public function getCache($key,$default=false) {
        if($this->onCache) {
            $value = Redis::get($key,$default);
            $newValue = json_decode($value);
            if($newValue) {
                return $newValue;
            }
            return $value;
        }
        return false;
    }

    public function setCache($key,$value) {
        if($this->onCache) {
            if(is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            return Redis::set($key,$value);
        }
        return false;
    }

    public function multiSetCache(Array $data) {
        Redis::pipeline(function($pipe) use ($data)
        {
            foreach ($data as $item) {
                $pipe->set($item['key'], $item['value']);
            }
        });
    }

    public function removeCache($key) {
        if($this->onCache) {
            return Redis::del($key);
        }
    }
}
