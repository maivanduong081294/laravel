<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "name",
        "icon",
        "route_id",
        "group_ids",
        "user_ids",
        "parent_id",
        "hidden",
        "status"
    ];

    protected $attributes = [
        "status" => 0,
        "hidden" => 0,
    ];

    protected $nameCol = 'name';

    public function __construct(Array $attributes = []) {
        if(!in_array(self::getPerPage(), listShowItemsNumber())) {
            self::setPerPage(session('perPage',20));
        }
        parent::__construct($attributes);
    }

    public function getListIndex($where=[],$orderBy=[]) {
        $perPage = self::getPerPage();
        $currentPage = isset($_REQUEST['page'])?$_REQUEST['page']:1;
        $keyCache = __FUNCTION__.'-'.json_encode(array_merge($where,$orderBy)).'-'.$perPage.'-'.$currentPage;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::selectRaw('permissions.*, routes.name as route_name, routes.title as route_title');
            $query = self::setWhere($query,$where);
            $query = self::setOrder($query,$orderBy);
            $query = $query->leftJoin('routes', 'routes.id', '=', 'permissions.route_id');
            $result = $query->paginate($perPage)->withQueryString();
            $value = $result->count() > 0?$result:[];
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getSelectedRouteIds($route_id=null) {
        $keyCache = __FUNCTION__.json_encode($route_id);
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $query = self::selectRaw('distinct(route_id) as id');
            if(!empty($route_id)) {
                if(is_array($route_id)) {
                    $query->whereIn('route_id',$route_id);
                }
                else {
                    $query->where('route_id','!=',$route_id);
                }
            }
            $result = $query->get();
            $value = [];
            if($result->count() > 0) {
                foreach($result as $item) {
                    $value[] = $item->id;
                }
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function filterData($data) {
        $data = parent::filterData($data);
        return $data;
    }
}
