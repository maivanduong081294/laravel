@extends('layouts.auth')

@section('title')
    {{$title}}
@endsection

@section('alert')
    @if (session()->get('msg'))
        <x-alert message="{{session()->get('msg')}}" type="{{session()->get('type')}}" />
    @endif
@endsection

@section('content')
	<h2>Đăng ký tài khoản</h2>
    <form action="{{route('register')}}" method="post">
        <x-form.input type="text" name="fullname" placeholder="Họ và tên" value="{{request()->old('fullname')}}" icon="<i class='fa-solid fa-user'></i>"/>
        <x-form.input type="text" name="email" placeholder="Email" value="{{request()->old('email')}}" icon="<i class='fa-solid fa-envelope'></i>"/>
        <x-form.input type="text" name="username" placeholder="Tên đăng nhập" value="{{request()->old('username')}}" icon="<i class='fa-solid fa-user'></i>"/>
        <x-form.input type="password" name="password" placeholder="Mật khẩu" value="{{request()->old('password')}}" icon="<i class='fa-solid fa-lock'></i>"/>
        <x-form.input type="password" name="re-password" placeholder="Xác nhận mật khẩu" value="{{request()->old('re-password')}}" icon="<i class='fa-solid fa-lock'></i>"/>
        <x-form.check name="policy" id="policy" checked="{{request()->old('policy') == 1?'true':'false'}}" value="1" label="Đồng ý với chính sách và điều khoản"/>
        <div class="form-footer">
            <input type="submit" value="Đăng ký">
            <p>Đã có tài khoản <span>→</span> <a href="{{route('login')}}"> Đăng nhập</a></p>
            <div class="clear"></div>
        </div>
        @csrf
    </form>
@endsection