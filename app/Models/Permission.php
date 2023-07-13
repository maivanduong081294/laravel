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



    public function filterData($data) {
        $data = parent::filterData($data);
        return $data;
    }
}
