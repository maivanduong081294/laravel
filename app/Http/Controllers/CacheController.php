<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redis;

class CacheController extends Controller
{
    //

    private $onCache = true;

    public function get($key) {
        if($this->onCache) {
            $redis = Redis::connection();
            return $redis->get($key);
        }
        return false;
    }

    public function set($key,$value) {
        if($this->onCache) {
            $redis = Redis::connection();
            return $redis->set($key,$value);
        }
        return false;
    }

    public function multiSet(Array $data) {
        Redis::pipeline(function($pipe) use ($data)
        {
            foreach ($data as $item) {
                $pipe->set($item['key'], $item['value']);
            }
        });
    }

    public function remove($key) {
        if($this->onCache) {
            $redis = Redis::connection();
            return $redis->del($key);
        }
    }
}
