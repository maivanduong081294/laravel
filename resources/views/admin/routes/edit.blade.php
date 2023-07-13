@extends('layouts.admin')

@section('title')
    {{$title}}
@endsection

@section('heading')
    {{$heading}}
@endsection

@section('breadcrumb')
   <x-admin.breadcrumb data="{{json_encode($breadcrumb)}}" />
@endsection

@section('alert')
    @if (session()->get('msg'))
        <x-alert message="{{session()->get('msg')}}" type="{{session()->get('type')}}" />
    @endif
@endsection

@section('content')
<div class="detail">
    <form class="detail-form" method="POST">
        <div class="detail-main">
            <div class="form-group">
                <div class="form-group-heading">
                    <div class="form-group-heading-wrapper">
                        <i class="fa-solid fa-circle-info"></i>
                        <h2>Thông tin</h2>
                    </div>
                </div>
                <div class="form-group-body">
                    <x-form.input label="Tiêu đề định tuyến" value="{{old('title') ?? $detail->title }}" id="title" name="title" placeholder="true"/>
                    <x-form.input label="Name" value="{{$detail->name}}" readonly="true" id="name" name="name" placeholder="true"/>
                    <x-form.input label="Đường dẫn" value="{{old('uri') ?? $detail->uri}}" id="uri" name="uri" placeholder="true"/>
                </div>
            </div>

            <div class="form-group">
                <div class="form-group-heading">
                    <div class="form-group-heading-wrapper">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        <h2>Cấu hình</h2>
                    </div>
                </div>
                <div class="form-group-body">
                    @php
                        $method = old('method') ? json_encode(old('method')) : json_encode(explode(",",$detail->method));
                    @endphp
                    <x-form.select default="Chọn methods" label="Methods" selected="{{$method}}" id="method" placeholder="true" multiple="true" name="method[]" keyByValue="1" values="{{json_encode(routeMethods())}}"/>
                    <x-form.select default="Chọn Controller" label="Controller" selected="{{old('controller') ?? $detail->controller}}" id="controller" name="controller" keyByValue="1" values="{{json_encode(listAdminController(true))}}"/>
                    <x-form.input label="Function" value="{{old('function') ?? $detail->function}}" id="function" name="function" placeholder="true"/>
                    <x-form.input label="Middleware" value="{{old('middleware') ?? $detail->middleware}}" id="middleware" name="middleware" placeholder="true"/>
                </div>
            </div>

            <div class="form-group">
                <div class="form-group-heading">
                    <div class="form-group-heading-wrapper">
                        <i class="fa-solid fa-calendar-check"></i>
                        <h2>Tuỳ chọn</h2>
                    </div>
                </div>
                <div class="form-group-body">
                    <x-form.yesno label="Ẩn phân quyền" value="{{old('hidden') ?? $detail->hidden}}" id="hidden" name="hidden" />
                    <x-form.yesno label="Trạng thái" value="{{old('status') ?? $detail->status}}" yes="Hoạt động" no="Không hoạt động" id="status" name="status" />
                </div>
            </div>
        </div>
        <div class="detail-sidebar">
            <div class="detail-sidebar-wrapper">
                <div class="detail-sidebar-group detail-actions">
                    <x-styles.button type="submit" content="Save" left-icon='<i class="fa-solid fa-floppy-disk"></i>' />
                    <x-styles.button class="btn-danger" href="{{route('admin.routes')}}" right-icon='<i class="fa-solid fa-right-to-bracket"></i>' content="Quay lại" />
                </div>
            </div>
        </div>
        @method('POST')
        @csrf
    </form>
</div>
@endsection