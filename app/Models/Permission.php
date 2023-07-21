<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "name",
        "name_has_children",
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

    public function checkPermissionByRouteId($route_id) {
        $user = User::getCurrentUser();
        $user_id = $user->id;
        $keyCache = __FUNCTION__.'-'.$route_id.'-'.$user_id;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $links = (array) self::getRouteLinks();
            $where = [
                ['status',1],
                ['route_id',$route_id]
            ];
            $sql = "FIND_IN_SET(?, user_ids)";
            $sqlData = [$user_id];

            $sql.= " OR FIND_IN_SET(?, group_ids)";
            $sqlData[] = $user->group_id;
            $sql = "({$sql})";
            $where[] = [
                'raw' => [$sql, $sqlData],
            ];
            $results = self::getDetail($where);
            if($results) {
                $value = true;
            }
            else {
                $value = false;
            }
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

    private function getRouteIds($id=0,$route_ids=[]) {
        $keyCache = __FUNCTION__.'-'.$id.'-'.implode(',',$route_ids);
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = [
                ['status',1],
                ['parent_id',$id],
                ['hidden',0]
            ];
            $results = self::getAllList($where);
            if($results) {
                foreach($results as $item) {
                    $route_ids[] = $item->route_id;
                    $route_ids = self::getRouteIds($item->id,$route_ids);
                }
            }
            $value = $route_ids;
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    private function getRouteLinks() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $route_ids = self::getRouteIds();
            if($route_ids) {
                $route = new Route();
                $results = $route->getListByIds($route_ids);
                if($results) {
                    $links = [];
                    foreach($results as $item) {
                        $links[$item->id] = getLinkByRouteName($item->name);
                    }
                    //$links = json_decode(json_encode($links));
                }
                $value = $links;
            }
            else {
                $value = [];
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function getMenu($id=0) {
        $user = User::getCurrentUser();
        $user_id = $user->id;
        $keyCache = __FUNCTION__.'-'.$id.'-'.$user_id;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $links = (array) self::getRouteLinks();
            $where = [
                ['hidden',0],
                ['status',1],
                ['parent_id',$id]
            ];
            $sql = "FIND_IN_SET(?, user_ids)";
            $sqlData = [$user_id];

            $sql.= " OR FIND_IN_SET(?, group_ids)";
            $sqlData[] = $user->group_id;
            $sql = "({$sql})";
            $where[] = [
                'raw' => [$sql, $sqlData],
            ];
            $results = self::getAllList($where);
            $results = json_decode(json_encode($results));
            if($results) {
                $data = [];
                foreach($results as $item) {
                    $route_id = $item->route_id."";
                    $children = self::getMenu($item->id);
                    $link = $links[$route_id];
                    $itemData = [
                        'name' => $item->name,
                        'icon' => trim($item->icon)?'<i class="'.$item->icon.'"></i>':"",
                        'link' => $link,
                    ];
                    if($children) {
                        $child = $itemData;
                        $child['name'] = $item->name_has_children?$item->name_has_children:$item->name;
                        $children = array_merge([$child],$children);
                    }
                    if($children) {
                        $itemData['children'] = $children;
                    }
                    if($link) {
                        $data[] = $itemData;
                    }
                }
                $value = $data;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }

    public function showMenu() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $menu = self::getMenu();
            $menu = $menu?$menu:[];
            $beforeMenu = [
                [
                    'name' => 'Bảng điều khiển',
                    'link' => '/admin',
                    'icon' => '<i class="fa-solid fa-house"></i>'
                ],
            ];
        
            $afterMenu = [];
            if(User::isAdminUser()) {
                $afterMenu[] = [
                    'name' => 'Phân quyền',
                    'link' => '/admin/permissions',
                    'icon' => '<i class="fa-solid fa-arrows-to-eye"></i>',
                    'children' => [
                        [
                            'name' => 'Danh sách',
                            'link' => '/admin/permissions',
                        ],
                        [
                            'name' => 'Thêm mới',
                            'link' => '/admin/permissions/add',
                        ]
                    ]
                ];
            }
            if(User::isRootUser()) {
                $afterMenu[] = [
                    'name' => 'Định tuyến',
                    'link' => '/admin/routes',
                    'icon' => '<i class="fa-solid fa-route"></i>',
                    'children' => [
                        [
                            'name' => 'Danh sách',
                            'link' => '/admin/routes',
                        ],
                        [
                            'name' => 'Thêm mới',
                            'link' => '/admin/routes/add',
                        ]
                    ]
                ];
            }
            $value = array_merge($beforeMenu,$menu,$afterMenu);
            $value = json_decode(json_encode($value));
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
