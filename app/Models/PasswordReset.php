<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'token', 'status'
    ];

    protected $attributes = [
        'status' => 0,
    ];

    const UPDATED_AT = null;

    public function create($data) {
        $check = parent::where('email',$data['email'])->where('status',0)->first();
        if(!$check) {
            return parent::create($data);
        }
        else {
            return false;
        }
    }
}
