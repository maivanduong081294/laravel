<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "link",
        "icon",
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
}
