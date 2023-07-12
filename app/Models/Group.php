<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'status'
    ];

    protected $attribtes = [
        "status" => 1,
    ];

    public function showList() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $where = [
                'status' => 1,
            ];
            $order = [
                [
                    'orderBy' => 'id',
                    'orderType' => 'ASC',
                ]
            ];
            $result = self::getAllList($where,$order);
            $value = [];
            foreach($result as $item) {
                $value[$item->id] = $item->name;
            }
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
