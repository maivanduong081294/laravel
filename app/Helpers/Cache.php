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

function removeCache($key) {
    if(enabledCache()) {
        return Cache::pull($key);
    }
}

function flushCache() {
    if(enabledCache()) {
        return Cache::flush();
    }
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

function flushTagsCache($tag) {
    if(enabledCache()) {
        return Cache::tags($tag)->flush();
    }
}

function handleGetValue($value) {
    $newValue = json_decode($value);
    if($newValue) {
        return $newValue;
    }
    return $value;
}

function handleSetValue($value) {
    if(is_array($value) || is_object($value)) {
        $value = json_encode($value);
    }
    return $value;
}