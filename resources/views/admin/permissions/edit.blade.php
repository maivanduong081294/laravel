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
                    <x-form.input label="Tên phân quyền" value="{{old('name') ?? $detail->name}}" id="name" name="name" placeholder="true"/>
                    <x-form.input label="Icon" value="{{old('icon') ?? $detail->icon}}" id="icon" name="icon" placeholder="fa-solid fa-access"/>
                    <x-form.select default="Chọn quyền truy cập" label="Quyền truy cập" disabled="{{json_encode($selectedRoutes)}}" selected="{{old('route_id') ?? $detail->route_id}}" id="route_id" name="route_id" values="{{json_encode($listRoute)}}"/>
                </div>
            </div>

            <div class="form-group">
                <div class="form-group-heading">
                    <div class="form-group-heading-wrapper">
                        <i class="fa-solid fa-users"></i>
                        <h2>Phân quyền</h2>
                    </div>
                </div>
                <div class="form-group-body">
                    @php
                        $group_ids = old('method') ? json_encode(old('method')) : json_encode(explode(",",$detail->group_ids));
                        $user_ids = old('method') ? json_encode(old('method')) : json_encode(explode(",",$detail->user_ids));
                    @endphp
                    <x-form.list-check all="1" label="Nhóm người dùng" selected="{{$group_ids}}" id="group_ids" placeholder="true" multiple="true" name="group_ids[]" values="{{json_encode($listGroup)}}"/>
                    <x-form.select label="Tài khoản" selected="{{$user_ids}}" id="user_ids" placeholder="true" multiple="true" name="user_ids[]" values="{{json_encode($listUser)}}"/>
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
                    <x-form.yesno label="Ẩn Menu" value="{{old('hidden') ?? $detail->hidden}}" id="hidden" name="hidden" />
                    <x-form.yesno label="Trạng thái" value="{{old('status') ?? $detail->status}}" yes="Hoạt động" no="Không hoạt động" id="status" name="status" />
                </div>
            </div>
        </div>
        <div class="detail-sidebar">
            <div class="detail-sidebar-wrapper">
                <div class="form-group">
                    <div class="form-group-heading">
                        <div class="form-group-heading-wrapper">
                            <i class="fa-solid fa-circle-info"></i>
                            <h2>Phân quyền cha</h2>
                        </div>
                    </div>
                    <div class="form-group-body">
                        <x-form.list-check one-value="true" style="list" default="Chọn phân quyền cha" selected="{{old('parent_id') || $detail->parent_id}}" id="parent_id" name="parent_id" values="{{json_encode($listPermission)}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group-body detail-actions">
                        <x-styles.button type="submit" content="Save" left-icon='<i class="fa-solid fa-floppy-disk"></i>' />
                        <x-styles.button class="btn-danger" href="{{route('admin.permissions')}}" right-icon='<i class="fa-solid fa-right-to-bracket"></i>' content="Quay lại" />
                    </div>
                </div>
            </div>
        </div>
        @method('POST')
        @csrf
    </form>
</div>
@endsection