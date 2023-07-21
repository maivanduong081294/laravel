<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TinyPNG extends BaseModel
{
    use HasFactory;

    protected $table = 'api_tinypng';

    protected $fillable = ['api','month_limit','status','count'];

    protected $attributes = [
        'month_limit' => 0,
        'status' => 1,
        'count' => 0,
    ];

    public $timestamps = false;
}
