<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mediable extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "media_id",
        "type",
        "remove_bg",
        "webp",
    ];

    protected $attributes = [
        "remove_bg" => 0,
        "webp" => 0,
    ];

    public $timestamps = false;

}
