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
    <h2>Đặt lại mật khẩu</h2>
    <form action="{{route('handle-reset-password')}}" method="post">
        <x-form.input type="text" name="password" placeholder="Mật khẩu" value="{{old('password')}}" icon="<i class='fa-solid fa-envelope'></i>"/>
        <x-form.input type="text" name="re-password" placeholder="Xác nhận mật khẩu" value="{{old('re-password')}}" icon="<i class='fa-solid fa-envelope'></i>"/>
        <div class="form-footer">
            <input type="submit" value="Xác nhận">
            <p>Bạn đã nhớ tài khoản <span>→</span> <a href="{{route('login')}}"> Đăng nhập</a></p>
            <div class="clear"></div>
        </div>
        @csrf
    </form>
@endsection