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
}
