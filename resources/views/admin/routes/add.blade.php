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
                    <i class="fa-solid fa-circle-info"></i>
                    <h2>Thông tin</h2>
                </div>
                <div class="form-group-body">
                    <x-form.input label="Tên định tuyến" value="{{old('title')}}" id="title" name="title" placeholder="true"/>
                    <x-form.input label="Đường dẫn" value="{{old('uri')}}" id="uri" name="uri" placeholder="true"/>
                    <x-form.select default="Chọn methods" label="Methods" selected="{{json_encode(old('method'))}}" id="method" placeholder="true" multiple="true" name="method[]" keyByValue="1" values="{{json_encode(routeMethods())}}"/>
                    <x-form.select default="Chọn Controller" label="Controller" selected="{{old('controller')}}" id="controller" name="controller" keyByValue="1" values="{{json_encode(listAdminController(true))}}"/>
                    <x-form.input label="Function" value="{{old('function')}}" id="function" name="function" placeholder="true"/>
                    <x-form.input label="Middleware" value="{{old('middleware')}}" id="Middleware" name="Middleware" placeholder="true"/>
                    <x-form.select default="Chọn đinh tuyến cha (nếu có)" label="Thuộc đinh tuyến" selected="{{old('parent_id')}}" id="parent_id" name="parent_id" values="{{json_encode($treeRoute)}}"/>
                    <x-form.select label="Hiển thị phân quyền" selected="{{old('hidden') ?? 0}}" id="hidden" name="hidden" values="{{json_encode($hiddenList)}}"/>
                    <x-form.select label="Trạng thái" selected="{{old('status') ?? 1}}" id="status" name="status" values="{{json_encode($statusList)}}"/>
                </div>
            </div>
        </div>
        <div class="detail-sidebar">
            <x-styles.button type="submit" content="Save" /> 
        </div>
        @method('POST')
        @csrf
    </form>
</div>
@endsection