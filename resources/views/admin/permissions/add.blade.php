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
                    <x-form.input label="Tên phân quyền" value="{{old('title')}}" id="title" name="title" placeholder="true"/>
                    <x-form.input label="Icon" value="{{old('icon')}}" id="icon" name="icon" placeholder="fa-solid fa-access"/>
                    <x-form.select default="Chọn quyền truy cập" label="Quyền truy cập" selected="{{old('route_id')}}" id="route_id" name="route_id" values="{{json_encode($listRoute)}}"/>
                    <x-form.select default="Chọn nhóm" label="Nhóm người dùng" selected="{{json_encode(old('group_ids'))}}" id="group_ids" placeholder="true" multiple="true" name="group_ids[]" values="{{json_encode($listGroup)}}"/>
                    <x-form.select default="Chọn tài khoản" label="Tài khoản" selected="{{json_encode(old('user_ids'))}}" id="user_ids" placeholder="true" multiple="true" name="user_ids[]" values="{{json_encode($listUser)}}"/>
                    <x-form.select label="Hiển thị Menu" selected="{{old('hidden') ?? 0}}" id="hidden" name="hidden" values="{{json_encode($listHidden)}}"/>
                    <x-form.select default="Chọn Menu cha" label="Menu cha" selected="{{old('parent_id')}}" id="parent_id" name="parent_id" values="{{json_encode($listPermission)}}"/>
                    <x-form.select label="Trạng thái" selected="{{old('status') ?? 1}}" id="status" name="status" values="{{json_encode($listStatus)}}"/>
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