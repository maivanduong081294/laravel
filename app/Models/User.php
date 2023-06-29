<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Mail; 
use Auth;
use Hash;
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

    public function create($data) {
        $token = Str::random(64);
        $data['email_verified_token'] = $token;
        $createUser = parent::create($data);
        
        if($createUser) {
            Mail::send('email.verificationEmail', ['token' => $token], function($message) use($data){
                $message->to($data['email']);
                $message->subject('Email Verification Mail');
            });
        }

        return $createUser;
    }

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

    public function forgotPassword($email) {
        $token = Str::random(64);
        $data = array(
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        );

        $result = PasswordReset::create($data);

        if($result) {
            Mail::send('email.forgotPasswordEmail', ['token' => $token], function($message) use($data){
                $message->to($data['email']);
                $message->subject('Email Forgot Password Mail');
            });
        }

        return true;
    }

    public function resetPassword($token,$password) {
        $email = PasswordReset::where('token',$token)->first();
        if($email && $email->email) {
            $email = $email->email;
            $password = Hash::make($password);
            parent::where('email', $email)->update(['password' => $password]);
            PasswordReset::where('email', $email)->update(['status' => 1]);
            return true;
        }
        return false;
    }
}
