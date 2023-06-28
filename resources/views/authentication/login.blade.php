@extends('layouts.authenticationLayout')

@section('title')
    {{$title ?? 'Login'}}
@endsection

@section('content')
	<h2>Đăng nhập</h2>
    <form action="{{route('login')}}" method="post">
        <x-form.input type="text" name="username" placeholder="Tên đăng nhập hoặc Email" value="{{old('username')}}" icon="<i class='fa-solid fa-user'></i>"/>
        <x-form.input type="password" name="password" placeholder="Mật khẩu" value="{{old('password')}}" icon="<i class='fa-solid fa-lock'></i>"/>
        <div class="form-action">
            <div class="action-item">
                <label for="remember"><input type="checkbox" id="remember" name="remember" value="1"><span></span>Lưu mật khẩu</label>
            </div>
            <div class="action-item">
                <a href="#">Quên mật khẩu?</a>
            </div>
        </div>
        <div class="form-footer">
            <input type="submit" value="Đăng nhập">
            <p>Chưa có tài khoản <span>→</span> <a href="{{route('register')}}"> Đăng ký</a></p>
            <div class="clear"></div>
        </div>
        @csrf
    </form>
@endsection