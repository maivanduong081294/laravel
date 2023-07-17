<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "name",
        "file_name",
        "disk",
        "mime_type",
        "type",
        "author_id",
    ];
}
