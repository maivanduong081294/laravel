<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordReset extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'email', 'token', 'status'
    ];

    protected $attributes = [
        'status' => 0,
    ];

    const UPDATED_AT = null;
}
