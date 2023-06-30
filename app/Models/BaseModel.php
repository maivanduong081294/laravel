<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function hasCache($key) {
        return hasTagsCache([self::getTableName()],$key);
    }
    public function getCache($key) {
        return getTagsCache([self::getTableName()],$key);
    }

    public function setCache($key,$value,$expire=null) {
        return setTagsCache([self::getTableName()],$key,$value,$expire);
    }

    public function getListByWhere(array $wheres) {
        if(!empty($wheres)) {
            $keyCache = __FUNCTION__;
            $keyCache.= "-".json_encode($wheres);
            $value = self::getCache($keyCache);
            if(self::hasCache($keyCache)) {
                return $value;
            }
            else {
                $query = self::select();
                foreach($wheres as $key => $item) {
                    if(is_array($item)) {
                        $name = $item['name'];
                        $method = isset($item['method'])?$item['method']:'=';
                        $value = $item['value'];
                        $query->where($name,$method,$value);
                    }
                    else {
                        $query->where($key,$item);
                    }
                }
                $result = $query->get();
                $value = $result?$result:'';
                self::setCache($keyCache,$value);
            }
            return $value;
        }
        return [];
    }

    public function getDetailByWhere(array $wheres) {
        if(!empty($wheres)) {
            $keyCache = __FUNCTION__;
            $keyCache.= "-".json_encode($wheres);
            $value = self::getCache($keyCache);
            if(self::hasCache($keyCache)) {
                return $value;
            }
            else {
                $query = self::select();
                foreach($wheres as $key => $item) {
                    if(is_array($item)) {
                        $name = $item['name'];
                        $method = isset($item['method'])?$item['method']:'=';
                        $value = $item['value'];
                        $query->where($name,$method,$value);
                    }
                    else {
                        $query->where($key,$item);
                    }
                }
                $result = $query->first();
                $value = $result?$result:'';
                self::setCache($keyCache,$value);
            }
            return $value;
        }
        return [];
    }
}
