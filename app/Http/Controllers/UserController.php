<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;

class UserController extends Controller
{
    //
    public function Login(Request $request) {
        $title = 'Đăng nhập';
        if($request->method() === "POST") {
            Auth::logout();
            $rules = [
                'username' => 'required',
                'password' => 'required',
            ];
            $messages = [
                'username.required' => 'Vui lòng nhập tên đăng nhập hoặc email',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ];
            $validate = Validator::make($request->all(),$rules,$messages);
            $validate->validate();
            // if($validate->fails()) {
            //     return $validate->errors();
            // }

            $usernameLogin = [
                'username' => $request->username,
                'password' => $request->password,
            ];
            $emailLogin = [
                'email' => $request->username,
                'password' => $request->password,
            ];
            $remember = $request->remember?true:false;
            if (Auth::attempt($usernameLogin,$remember) || Auth::attempt($emailLogin,$remember)) {
                return redirect()->route('admin');
            } else {
                return back()->withInput();
            }
        }
        return view('authentication.login',compact('title'));
    }

    public function register(Request $request) {
        $title = 'Đăng ký tài khoản';
        if($request->method() === "POST") {
            $password = $request->password;
            $rules = [
                'fullname' => 'required',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|min:4|unique:users,username',
                'password' => ['required','min:8'],
                're-password' => ['required',function($attribute, $value, $fail) use ($password) {
                    if($value !== $password) {
                        $fail("Mật khẩu không trùng khớp");
                    }
                }],
                'policy' => 'required'
            ];

            $messages = [
                'fullname.required' => 'Vui lòng nhập họ và tên',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Vui lòng nhập đúng định dạng email',
                'email.unique' => 'Email đã tồn tại trong hệ thống',
                'username.required' => 'Vui lòng nhập tên đăng nhập',
                'username.unique' => 'Tên đăng nhập đã tồn tại trong hệ thống',
                'username.min' => 'Tên đăng nhập phải có ít nhất :min ký tự',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất :min ký tự',
                're-password.required' => 'Vui lòng nhập xác nhận mật khẩu',
                'policy.required' => 'Bạn chưa chấp nhận chính sách và điều khoản',
            ];
            $validate = Validator::make($request->all(),$rules,$messages);
            $validate->validate();
        }
        return view('authentication.register',compact('title'));
    }
}
