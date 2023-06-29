<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Validator;
use App\Models\User;
use App\Models\PasswordReset;

class UserController extends Controller
{
    //
    public function Login(Request $request) {
        $title = 'Đăng nhập';
        echo $request->msg;
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
            $validator = Validator::make($request->all(),$rules,$messages);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('msg','Đăng nhập không thành công');
            }

            $username = $request->username;
            $password = $request->password;
            $remember = $request->remember?true:false;
            if (User::login($username,$password,$remember)) {
                return redirect()->route('admin');
            } else {
                return back()->withInput()->with('msg','Đăng nhập không thành công');
            }
        }
        return view('authentication.login',compact('title'));
    }

    public function logout() {
        session()->flush();
        Auth::logout();

        return redirect()->route('login');
    }

    public function register(Request $request) {
        $title = 'Đăng ký tài khoản';
        if($request->method() === "POST") {
            $password = $request->password;
            $rules = [
                'fullname' => 'required',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|min:4|unique:users,username',
                'password' => ['required'],
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
                'email.email' => 'Email không đúng định dạng',
                'email.unique' => 'Email đã tồn tại trong hệ thống',
                'username.required' => 'Vui lòng nhập tên đăng nhập',
                'username.unique' => 'Tên đăng nhập đã tồn tại trong hệ thống',
                'username.min' => 'Tên đăng nhập phải có ít nhất :min ký tự',
                'password.required' => 'Vui lòng nhập mật khẩu',
                're-password.required' => 'Vui lòng nhập xác nhận mật khẩu',
                'policy.required' => 'Bạn chưa chấp nhận chính sách và điều khoản',
            ];
            $validator = Validator::make($request->all(),$rules,$messages);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('msg', 'Đăng ký không thành công');
            }
            else {
                $data = [
                    'fullname' => $request->fullname,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'email' => $request->email,
                ];
                $createUser = User::create($data);

                return redirect()->route('login')->with(['msg'=> 'Đăng ký thành công kiểm tra email để kích hoạt','type'=>'success']);
            }
        }
        return view('authentication.register',compact('title'));
    }

    public function verify(Request $request) {
        $token = $request->token;
        if($token) {
            $user = User::where('email_verified_token',$token)->first();
            if($user) {
                if(!$user->email_verified_at) {
                    $update = [
                        'email_verified_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                    ];
                    User::where('id',$user->id)->update($update);
                }
                return redirect()->route('login')->with(['msg'=>'Kích hoạt thành công','type' => 'success']);
            }
        }
        return abort(404);
    }

    public function forgotPassword(Request $request) {
        $title = 'Quên mật khẩu';
        if($request->method() === 'POST') {
            $rules = [
                'email' => [
                    'required',
                    'email',
                    function($attribute,$value,$fail) use ($request) {
                        $user = User::where('email',$request->email)->first();
                        if(!$user) {
                            $fail('Email này chưa có trong hệ thống');
                        }
                    }
                ],
            ];
            $messages = [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng'
            ];
            $validator = Validator::make($request->all(),$rules,$messages);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('msg', 'Yêu cầu không hợp lệ');
            }
            else {
                $status = User::forgotPassword($request->email);
                if($status) {
                    return back()->withInput()->with(['msg'=> 'Kiểm tra email để đổi lại mật khẩu mới','type' => 'success']);
                }
                else {
                    return back()->withInput()->with('msg', 'Yêu cầu không hợp lệ');
                }
            }
        }
        return view('authentication.forgotPassword',compact('title'));
    }

    public function resetPassword(Request $request) {
        $title = 'Đặt lại mật khẩu';
        $token = $request->token;
        if($token) {
            $result = PasswordReset::where('token',$token)->first();
            if($result && $result->status == 0) {
                $request->session()->flash('token', $token);
                return view('authentication.resetPassword',compact('title'));
            }
        }
        return redirect()->route('forgot-password')->with('msg','Đường dẫn đã hết hạn');
    }

    public function handleResetPassword(Request $request) {
        $token = $request->session()->get('token');
        if($token) {
            $result = PasswordReset::where('token',$token)->first();
            if($result && $result->status == 0) {
                $password = $request->password;
                $rules = [
                    'password'=>'required',
                    're-password'=>['required', function($attribute,$value,$fail) use ($password) {
                        if($value != $password) {
                            $fail("Mật khẩu không trùng khớp");
                        }
                    }],
                ];
                $messages = [
                    'password.required' => 'Vui lòng nhập mật khẩu',
                    're-password.required' => 'Vui lòng nhập xác nhận mật khẩu',
                ];

                $validator = Validator::make($request->all(),$rules,$messages);
                if($validator->fails()) {
                    return back()->withErrors($validator)->withInput()->with('msg','Đặt lại mật khẩu không thành công');
                }
                else {
                    $result = User::resetPassword($token,$password);
                    if($result) {
                        return redirect()->route('login')->with(['msg'=>'Đặt lại mật khẩu thành công','type'=>'success']);
                    }
                    else {
                        return back()->with(['msg'=>'Đặt lại mật khẩu không thành công']);
                    }
                }
            }
        }
        abort('404');
    }
}
