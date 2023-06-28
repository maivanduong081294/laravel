@extends('layouts.authenticationLayout')

@section('title')
    {{$title ?? 'Login'}}
@endsection

@section('content')
	<h2>Đăng ký tài khoản</h2>
    <form action="{{route('register')}}" method="post">
        <x-form.input type="text" name="fullname" placeholder="Họ và tên" icon="<i class='fa-solid fa-user'></i>"/>
        <x-form.input type="text" name="email" placeholder="Email" icon="<i class='fa-solid fa-envelope'></i>"/>
        <x-form.input type="text" name="username" placeholder="Tên đăng nhập" icon="<i class='fa-solid fa-user'></i>"/>
        <x-form.input type="password" name="password" placeholder="Mật khẩu" icon="<i class='fa-solid fa-lock'></i>"/>
        <x-form.input type="password" name="re-password" placeholder="Xác nhận mật khẩu" icon="<i class='fa-solid fa-lock'></i>"/>
        <x-form.check name="policy" id="policy" value="1" label="Đồng ý với chính sách và điều khoản"/>
        <div class="form-footer">
            <input type="submit" value="Đăng ký">
            <p>Đã có tài khoản <span>→</span> <a href="{{route('login')}}"> Đăng nhập</a></p>
            <div class="clear"></div>
        </div>
        @csrf
    </form>
@endsection