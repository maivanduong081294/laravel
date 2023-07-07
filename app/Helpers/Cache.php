<?php 
use Illuminate\Support\Facades\Cache;
function enabledCache() {
    return true;
}

function expireCache() {
    return now()->addMinutes(60);
}

function hasCache($key) {
    return enabledCache() && Cache::has($key);
}

function getCache($key,$default=false) {
    if(enabledCache()) {
        $value = Cache::get($key,$default);
        return handleGetValue($value);
    }
    return false;
}

function setCache($key,$value,$expire = null) {
    if(enabledCache()) {
        $value = handleSetValue($value);
        if($expire == null) {
            $expire = expireCache();
        }
        return Cache::put($key,$value,$expire);
    }
    return false;
}
function rememberCache($key,$value,$expire = null) {
    if(enabledCache()) {
        return Cache::remember($key,$expire,function() use($value) {
            return $value;
        });
    }
    return false;
}

function removeCache($key) {
    if(enabledCache()) {
        return Cache::pull($key);
    }
}

function flushCache() {
    return Cache::flush();
}

function hasTagsCache($tags,$key) {
    return enabledCache() && Cache::tags($tags)->has($key);
}

function getTagsCache(Array $tags,$key) {
    if(enabledCache()) {
        $value = Cache::tags($tags)->get($key);
        return handleGetValue($value);
    }
    return false;
}

function setTagsCache(Array $tags,$key,$value,$expire=null) {
    if(enabledCache()) {
        $value = handleSetValue($value);
        if($expire == null) {
            $expire = expireCache();
        }
        return Cache::tags($tags)->put($key, $value, $expire);
    }
    return false;
}
function rememberTagsCache(Array $tags,$key,$value,$expire=null) {
    if(enabledCache()) {
        return Cache::tags($tags)->remember($key,$expire,function() use($value) {
            return $value;
        });
    }
    return false;
}

function flushTagsCache($tag) {
    if(enabledCache()) {
        return Cache::tags($tag)->flush();
    }
}

function handleGetValue($value) {
    if(!empty($value)) {
        $newValue = json_decode($value);
        if($newValue) {
            return $newValue;
        }
    }
    return $value;
}

function handleSetValue($value) {
    if((is_array($value) || is_object($value))) {
        if(empty($value)) {
            $value = false;
        }
        else {
            $value = json_encode($value);
        }
    }
    return $value;
}