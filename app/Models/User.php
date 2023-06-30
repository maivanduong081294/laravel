<?php

namespace App\Models;

use App\Http\Controllers\CacheController;
use Auth;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\PasswordReset;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname','username', 'email', 'password','status','email_verified_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $attributes = [
        'status' => 0,
    ];
    
    protected $redirectTo = '/admin';

    public function login($username,$password,$remember=false) {
        self::setCache('123','123123123');
        $usernameLogin = [
            'username' => $username,
            'password' => $password,
        ];
        $emailLogin = [
            'email' => $username,
            'password' => $password,
        ];
        if (Auth::attempt($usernameLogin,$remember) || Auth::attempt($emailLogin,$remember)) {
            return true;
        } else {
            return false;
        }
    }

    public function resetPassword($token,$password) {
        $email = PasswordReset::where('token',$token)->first();
        if($email && $email->email) {
            $email = $email->email;
            $password = Hash::make($password);
            self::where('email', $email)->update(['password' => $password]);
            PasswordReset::where('email', $email)->update(['status' => 1]);
            return true;
        }
        return false;
    }
}
