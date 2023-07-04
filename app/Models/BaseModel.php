<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BaseModel extends Model
{
    use HasFactory;

    public $perPage = 25;
    public $orderBy = 'id';
    public $orderType = 'DESC';

    public function __construct(Array $init=[]) {
        if(isset($init['perPage'])) {
            $this->perPage = $init['perPage'];
        }
    }

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

    public function flushCache() {
        return flushTagsCache(self::getTableName());
    }

    public function setWhere($query,$where) {
        if(!empty($where)) {
            foreach($where as $key => $item) {
                if(is_array($item)) {
                    if(count($item) === 3) {
                        $query->where($item[0],$item[1],$item[2]);
                    }
                    elseif(count($item) === 2) {
                        $query->where($item[0],$item[1]);
                    }
                }
                else {
                    if($key != 0) {
                        $query->where($key,$item);
                    }
                    else {
                        if(count($where) === 3) {
                            $query->where($where[0],$where[1],$where[2]);
                            break;
                        }
                        elseif(count($where) === 2) {
                            $query->where($where[0],$where[1]);
                            break;
                        }
                    }
                }
            }
        }
        return $query;
    }

    public function setOrder($query, $orderBy) {
        $order = [
            [
                'orderBy' => $this->orderBy,
                'orderType' => $this->orderType
            ]
        ];
        if(!empty($orderBy)) {
            $order = $orderBy;
        }
        foreach($order as $item) {
            $orderBy = trim($item['orderBy']);
            if($orderBy) {
                $orderType = isset($item['orderType'])?trim($item['orderType']):"DESC";
                $query->orderBy($orderBy,$orderType);
            }
        }
        return $query;
    }

    public function getList(Array $where=[],Array $orderBy=[]) {
        $keyCache = __FUNCTION__.'-'.json_encode(array_merge($where,$orderBy)).'-'.$this->perPage;
        $value = self::getCache($keyCache);
        if(1 || !self::hasCache($keyCache)) {
            $query = self::select();
            $query = self::setWhere($query,$where);
            $query = self::setOrder($query,$orderBy);
            $result = $query->paginate($this->perPage)->withQueryString();
            $value = $result?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
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
            if(!self::hasCache($keyCache)) {
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

    public function getListByStatus($status=1,$method='=') {
        $keyCache = __FUNCTION__;
        $keyCache.= "-".json_encode($status);
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::select()->where('status',$method,$status);
            $result = $query->get();
            $value = $result?$result:false;
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
