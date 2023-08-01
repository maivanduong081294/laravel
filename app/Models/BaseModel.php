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

    protected $nameCol = 'title';

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

    public function setWhere($query,$where,$orWhere = false,$group = false) {
        $from = $query->getQuery()->from;
        $method = $orWhere==false?'where':'orWhere';
        $methodRaw = $orWhere==false?'whereRaw':'orWhereRaw';
        $otherMethod = $method=='where'?'orWhere':'where';
        if(!empty($where)) {
            foreach($where as $key => $item) {
                if(is_array($item)) {
                    if($group) {
                        $query->$method(function ($query) use ($where,$orWhere) {
                            $newOrWhere = $orWhere?false:true;
                            $query = self::setWhere($query,$where,$newOrWhere);
                        });
                        break;
                    }
                    else {
                        $check = false;
                        foreach($item as $keyChild => $child) {
                            if($keyChild === 'raw') {
                                $query->$methodRaw($child[0],$child[1]);
                                $check = true;
                            }
                            elseif($keyChild === 'or') {
                                $check = true;
                                $query = self::setWhere($query,$child,true,true);
                                break;
                            }
                        }
                        if($check == false && count($where) > 0) {
                            if(isset($item[0]) && is_array($item[0])) {
                                $query->$method(function ($query) use ($item,$method,$otherMethod) {
                                    $i = 0;
                                    foreach($item as $sub) {
                                        if($i == 0) {
                                            if(count($sub) === 3) {
                                                $query->$method($sub[0],$sub[1],$sub[2]);
                                            }
                                            elseif(count($sub) === 2) {
                                                $query->$method($sub[0],$sub[1]);
                                            }
                                        }
                                        else {
                                            if(count($sub) === 3) {
                                                $query->$otherMethod($sub[0],$sub[1],$sub[2]);
                                            }
                                            elseif(count($sub) === 2) {
                                                $query->$otherMethod($sub[0],$sub[1]);
                                            }
                                        }
                                        $i++;
                                    }
                                });
                            }
                            else {
                                $query = self::setWhere($query,$item,$orWhere);
                            }
                        }
                    }
                }
                else {
                    if($key !== 0) {
                        $query->$method($key,$item);
                        break;
                    }
                    if(count($where) === 3) {
                        $query->$method($where[0],$where[1],$where[2]);
                        break;
                    }
                    elseif(count($where) === 2) {
                        $query->$method($where[0],$where[1]);
                        break;
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
            $value = $query->first();
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getDetailById(int $id) {
        $keyCache = __FUNCTION__.'-'.$id;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $value = self::find($id);
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
        $result = self::where('id',$id)->delete();
        if($result) {
            self::flushCache();
            return true;
        }
        return false;
    }
    public function deleteByWhere($where) {
        $query = self::select();
        $query = self::setWhere($query,$where);
        $result = $query->delete();
        if($result) {
            self::flushCache();
            return true;
        }
        return false;
    }

    public function deleteByIds(array $ids,Array $where=[]) {
        if(empty($ids)) {
            return false;
        }
        $query = self::select();
        $query = self::setWhere($query,$where);
        $result = $query->whereIn('id',$ids)->delete();
        if($result) {
            self::flushCache();
            return true;
        }
        return false;
    }

    public function create($data) {
        $data = $this->filterData($data);
        $result = parent::create($data);
        if($result) {
            $this->flushCache();
            return $result;
        }
        return false;
    }

    public function updateById($id,$data) {
        $data = $this->filterData($data);
        $result = self::where('id',$id)->update($data);
        if($result) {
            $this->flushCache();
            return true;
        }
        return false;
    }

    public function filterData($data) {
        $fillable = self::getFillable();
        $newData = [];
        foreach($fillable as $fill) {
            if(isset($data[$fill])) {
                if(is_array($data[$fill])) {
                    $newData[$fill] = implode(',',$data[$fill]);
                }
                else {
                    $newData[$fill] = $data[$fill];
                }
            }
        }
        return $newData;
    }

    public function getTree($where = []) {
        $keyCache = __FUNCTION__.'-'.json_encode($where);
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = array_merge([
                ['status',1],
                ['parent_id',0]
            ], $where);
            $results = self::getAllList($where);
            if($results) {
                $data = [];
                foreach($results as $item) {
                    $item->children = self::getChildren($item->id);
                    $data[] = $item;
                }
                $value = $data;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getChildren($id) {
        $keyCache = __FUNCTION__.'-'.$id;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = [
                ['status',1],
                ['parent_id',$id]
            ];
            $results = self::getAllList($where);
            if($results) {
                $data = [];
                foreach($results as $item) {
                    $item->children = self::getChildren($item->id);
                    $data[] = $item;
                }
                $value = $data;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getChildrenIds($id,$ids=[]) {
        $keyCache = __FUNCTION__.'-'.$id.'-'.implode(',',$ids);
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = [
                ['status',1],
                ['parent_id',$id]
            ];
            $results = self::getAllList($where);
            if($results) {
                foreach($results as $item) {
                    $ids[] = $item->id;
                    $ids = self::getChildrenIds($item->id,$ids);
                }
            }
            $value = $ids;
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function showTree($ids=[],$where=[], $parents=[], $parent_route = [],$prefix='') {
        $key = $this->nameCol;
        $keyCache = __FUNCTION__.json_encode($ids).'-'.$key.'-'.json_encode($where).'-'.json_encode($parents).json_encode($parent_route).'-'.$prefix;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            if(empty($parent_route)) {
                $result = self::getTree($where);
                $item = '';
            }
            else {
                $result = null;
                $item = $parent_route;
            }
            if(is_array($result)) {
                foreach($result as $item) {
                    if(((is_array($ids) && in_array($item->id,$ids)) || $ids == $item->id)) {
                        continue;
                    }
                    $parents[$item->id] = $item->$key;
                    if(!empty($item->children)) {
                        foreach($item->children as $child) {
                            $parents = self::showTree($ids,$where, $parents, $child);
                        }
                    }
                }
            }
            elseif($item) {
                if(((is_array($ids) && in_array($item->id,$ids)) || $ids == $item->id)) {
                    return $parents;
                }
                $prefix=$prefix.'---';
                $parents[$item->id] = $prefix.' '.$item->$key;
                if($item->children) {
                    foreach($item->children as $child) {
                        $parents = self::showTree($ids,$where, $parents, $child, $prefix);
                    }
                }
            }
            $value = $parents;
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
