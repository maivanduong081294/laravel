<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mediable extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "media_id",
        "type",
        "webp",
    ];

    protected $attributes = [
        "webp" => 0,
    ];

    public $timestamps = false;

}
