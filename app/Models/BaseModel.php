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

    public function getListByWhere($query,array $wheres) {
        if(!empty($wheres)) {
            $keyCache = self::getTableName();
            $keyCache.= '-getListByWhere';
            $keyCache.= "-".json_encode($wheres);
            $result = getCache($keyCache);
            if(!$result) {
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
                setCache($keyCache,$result);
            }
            return $result;
        }
        return [];
    }

    public function getDetailByWhere(array $wheres) {
        if(!empty($wheres)) {
            $keyCache = self::getTableName();
            $keyCache.= '-getDetailByWhere';
            $keyCache.= "-".json_encode($wheres);
            $result = getCache($keyCache);
            if(!$result) {
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
                setCache($keyCache,$result);
            }
            return $result;
        }
        return [];
    }
}
