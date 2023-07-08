<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BaseModel extends Model
{
    use HasFactory;
    public $orderBy = 'id';
    public $orderType = 'DESC';

    public function __construct(Array $attributes = []) {
        if(!in_array(self::getPerPage(), listShowItemsNumber())) {
            self::setPerPage(session('perPage',20));
        }
        parent::__construct($attributes);
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
        if(gettype($value) == 'object') {
            return rememberTagsCache([self::getTableName()],$key,$value,$expire);
        }
        return setTagsCache([self::getTableName()],$key,$value,$expire);
    }

    public function flushCache() {
        return flushTagsCache(self::getTableName());
    }

    public function setPerPage($number) {
        $number = setShowItemsNumber($number);
        return parent::setPerPage($number);
    }

    public function setWhere($query,$where) {
        if(!empty($where)) {
            foreach($where as $key => $item) {
                if(is_array($item)) {
                    if(is_array($item[0])) {
                        $query->where(function ($query) use ($item) {
                            $i = 0;
                            foreach($item as $sub) {
                                if($i == 0) {
                                    if(count($sub) === 3) {
                                        $query->where($sub[0],$sub[1],$sub[2]);
                                    }
                                    elseif(count($sub) === 2) {
                                        $query->where($sub[0],$sub[1]);
                                    }
                                }
                                else {
                                    if(count($sub) === 3) {
                                        $query->orWhere($sub[0],$sub[1],$sub[2]);
                                    }
                                    elseif(count($sub) === 2) {
                                        $query->orWhere($sub[0],$sub[1]);
                                    }
                                }
                                $i++;
                            }
                        });
                    }
                    else {
                        if(count($item) === 3) {
                            $query->where($item[0],$item[1],$item[2]);
                        }
                        elseif(count($item) === 2) {
                            $query->where($item[0],$item[1]);
                        }
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
    
    public function getAllList(Array $where=[],Array $orderBy=[]) {
        $keyCache = __FUNCTION__.'-'.json_encode(array_merge($where,$orderBy));
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::select();
            $query = self::setWhere($query,$where);
            $query = self::setOrder($query,$orderBy);
            $result = $query->get();
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getList(Array $where=[],Array $orderBy=[]) {
        $perPage = self::getPerPage();
        $currentPage = isset($_REQUEST['page'])?$_REQUEST['page']:1;
        $keyCache = __FUNCTION__.'-'.json_encode(array_merge($where,$orderBy)).'-'.$perPage.'-'.$currentPage;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::select();
            $query = self::setWhere($query,$where);
            $query = self::setOrder($query,$orderBy);
            $result = $query->paginate($perPage)->withQueryString();
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getDetail(Array $where=[]) {
        $keyCache = __FUNCTION__.'-'.json_encode(array_merge($where));
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::select();
            $query = self::setWhere($query,$where);
            $result = $query->first();
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getDetailById(int $id) {
        $keyCache = __FUNCTION__.'-'.$id;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $result = self::find($id);
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }
    public function getListByIds(array $ids,Array $where=[]) {
        if(empty($ids)) {
            return false;
        }
        sort($ids);
        $keyCache = __FUNCTION__.'-'.json_encode(array_merge($ids,$where));
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::select();
            $query = self::setWhere($query,$where);
            $result = $query->whereIn('id',$ids)->get();
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getListByStatus($status=1,$method='=') {
        $keyCache = __FUNCTION__;
        $keyCache.= "-".json_encode($status);
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::select()->where('status',$method,$status);
            $result = $query->get();
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function deleteById($id) {
        self::where('id',$id)->delete();
        self::flushCache();
        return true;
    }

    public function deleteByIds(array $ids,Array $where=[]) {
        if(empty($ids)) {
            return false;
        }
        $query = self::select();
        $query = self::setWhere($query,$where);
        $query->whereIn('id',$ids)->delete();
        self::flushCache();
        return true;
    }
}
