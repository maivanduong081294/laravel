<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Auth;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $msg = '';
        $subcriber = Group::where('name', 'Subscriber')->first();
        if(!Auth::user()) {
            $msg = 'Bạn không có quyền truy cập';
        }
        elseif(!Auth::user()->email_verified_at) {
            $msg = 'Vui lòng kiểm tra Email để xác thực tài khoản';
        }
        elseif(Auth::user()->status==0) {
            $msg = 'Tài khoản đã bị khoá';
        }
        elseif (!Auth::user()->group_id || Auth::user()->group_id == $subcriber->id) {
            $msg = 'Bạn không có quyền truy cập';
        }
        if(!$msg) {
            return $next($request);
        }
        else {
            return redirect()->route('login')->with('msg',$msg);     
        }
    }
}
