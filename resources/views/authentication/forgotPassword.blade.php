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
    <h2>Quên mật khẩu</h2>
    <form action="{{route('forgot-password')}}" method="post">
        <x-form.input type="text" name="email" placeholder="Email đã đăng ký" value="{{old('email')}}" icon="<i class='fa-solid fa-envelope'></i>"/>
        <div class="form-footer">
            <input type="submit" value="Gửi yêu cầu">
            <p>Bạn đã nhớ tài khoản <span>→</span> <a href="{{route('login')}}"> Đăng nhập</a></p>
            <div class="clear"></div>
        </div>
        @csrf
    </form>
@endsection