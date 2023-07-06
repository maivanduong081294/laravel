<?php

namespace App\Models;

use Auth;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;

use App\Models\PasswordReset;

class User extends BaseModel implements
AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract
{
    use HasFactory, Notifiable;
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
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
            PasswordReset::flushCache();
            return true;
        }
        return false;
    }

    public function getCurrentUser() {
        if(Auth::check()) {
            $user = Auth::user();
            $user->avatar = $user->avatar?$user->avatar:asset('assets/admin/images/default-avatar.png');
            return $user;
        }
        return false;
    }

    public function isAdminUser() {
        $check = self::isRootUser();
        if(!$check) {
            $user = self::getCurrentUser();
            if($user) {
                if($user->group_id == 1) {
                    $check = true;
                }
            }
        }
        return $check;
    }
    public function isRootUser() {
        $user = self::getCurrentUser();
        if($user) {
            if($user->id == 1) {
                return true;
            }
        }
        return false;
    }
}
