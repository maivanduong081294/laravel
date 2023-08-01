@extends('layouts.admin')

@section('title')
    {{$title}}
@endsection

@section('heading')
    {{$heading}}
@endsection

@section('heading-after')
    <x-styles.button href="{{route('admin.permissions.add')}}" content="Thêm mới"/>
@endsection

@section('breadcrumb')
   <x-admin.breadcrumb data="{{json_encode($breadcrumb)}}" />
@endsection

@php
    ob_start();   
@endphp
<tr>
    <th class="text-center"><x-styles.checkbox type="checkbox" input-class="check-list-all"/></th>
    <x-styles.th order-by="name" text="Tên phân quyền"/>
    <x-styles.th text="Icon"/>
    <x-styles.th text="Quyền truy cập"/>
    <x-styles.th text="Link"/>
    <x-styles.th text="Nhóm"/>
    <x-styles.th text="Tài khoản"/>
    <x-styles.th order-by="hidden" text="Menu"/>
    <x-styles.th order-by="status" text="Trạng thái"/>
    <x-styles.th text=""/>
</tr>
@php
    $columns = ob_get_contents();
    ob_end_clean();
@endphp

@section('alert')
    @if (session()->get('msg'))
        <x-alert message="{{session()->get('msg')}}" type="{{session()->get('type')}}" />
    @endif
@endsection

@section('content')
<div class="result">
    <div class="data-heading">
        <form action="" id="data-form">
            <div class="data-heading-top">
                <div class="data-heading-show-number">
                    <span>Hiển thị</span>
                    <x-styles.select key-by-value="1" id="set-show-items-number" selected="{{$perPage}}" values="{{listShowItemsNumber(true)}}"/>
                </div>
                <div class="search-data">
                    <x-styles.input type="text" name="search" value="{{request()->search}}" placeholder="Nhập từ khoá tìm kiếm" />
                    <x-styles.button type="submit" content="Tìm kiếm"/>
                </div>
            </div>
            <div class="data-heading-bottom">
                <div class="data-heading-actions">
                    <div class="data-heading-handle-checkbox handle-checkbox">
                        <x-styles.select class="list-handle-checkbox" default="Hành động" values="{{json_encode($actions)}}"/>
                        <x-styles.button class="btn-handle-checkbox" content="Áp dụng" />
                    </div>
                    <div class="data-heading-filter">
                        <x-styles.select default="Nhóm tài khoản" class="select2" name="group" selected="{{request()->group}}" values="{{json_encode($listGroup)}}"/>
                        <x-styles.select default="Tài khoản" class="select2" name="user" selected="{{request()->user}}" values="{{json_encode($listUser)}}"/>
                        <x-styles.select default="Trạng thái" name="status" selected="{{request()->status}}" values="{{json_encode($listStatus)}}"/>
                        <x-styles.select default="Menu" name="hidden" selected="{{request()->hidden}}" values="{{json_encode($listHidden)}}"/>
                        <x-styles.button type="submit" content="Lọc" />
                    </div>
                </div>
                @if(!empty($list))
                {{ $list->links() }}
                @endif
            </div>
            <input type="hidden" name="orderBy" value="{{request()->orderBy}}">
            <input type="hidden" name="orderType" value="{{request()->orderType}}">
        </form>
    </div>
    <div class="table-data">
        <table class="table">
            <thead>
                {!!$columns!!}
            </thead>
            <tbody>
                @if(empty($list))
                <tr>
                    <th colspan="8">Không có dữ liệu</th>
                </tr>
                @else
                @foreach($list as $item)
                    <tr>
                        <td class="min-col" class="text-center" data-title="Chọn"><x-styles.checkbox type="checkbox" class="check" name="id" input-class="check-list-item" value="{{$item->id}}"/></th>
                        <td data-title="Tên phân quyền">{{$item->name}}</td>
                        <td data-title="Icon" class="min-col"><i class="{{$item->icon}}"></i></td>
                        <td data-title="Quyền truy cập">{{$item->route_title}}</td>
                        <td data-title="Link" class="min-col">
                            @if (getLinkByRouteName($item->route_name))
                                
                                <x-styles.button href="{{getLinkByRouteName($item->route_name)}}" content='Truy cập'/>
                            @endif
                        </td>
                        <td data-title="Nhóm">
                            @php
                                $groups = explode(",",$item->group_ids);
                                $groupStr = '';
                                foreach ($listGroup as $key => $value) {
                                    if(in_array($key,$groups)) {
                                        if($groupStr) {
                                            $groupStr.= ", ";
                                        }
                                        $groupStr .= $value;
                                    }
                                }
                            @endphp
                            {{$groupStr}}
                        </td>
                        <td data-title="Tài khoản">
                            @php
                                $users = explode(",",$item->user_ids);
                                $userStr = '';
                                foreach ($listUser as $key => $value) {
                                    if(in_array($key,$users)) {
                                        if($userStr) {
                                            $userStr.= ", ";
                                        }
                                        $userStr .= $value;
                                    }
                                }
                            @endphp
                            {{$userStr}}
                        </td>
                        <td data-title="Menu" class="min-col"><x-styles.status value="{{$item->hidden}}" name="hidden" update="{{$item->hidden?0:1}}" /></td>
                        <td data-title="Trạng thái" class="min-col"><x-styles.status value="{{$item->status}}" name="status" update="{{$item->status?0:1}}"/></td>
                        <td class="text-center">
                            <x-styles.button href="{{route('admin.permissions.edit',$item->id)}}" class="btn-warning" content='<i class="fa-solid fa-pen"></i>'/>
                            <x-styles.button class="btn-delete-data btn-danger" data-item-id="{{$item->id}}" content='<i class="fa-solid fa-trash"></i>'/>
                        </td>
                    </tr>
                @endforeach
                @endif
            </tbody>
            <tfoot>
                {!!$columns!!}
            </tfoot>
        </table>
    </div>
    <div class="data-foot">
        <div class="data-foot-actions">
            <div class="data-foot-handle-checkbox handle-checkbox">
                <x-styles.select class="list-handle-checkbox" default="Hành động" values="{{json_encode($actions)}}"/>
                <x-styles.button class="btn-handle-checkbox" content="Áp dụng" />
            </div>
        </div>       
        @if(!empty($list))
        {{ $list->links() }}
        @endif
    </div>
</div>
@endsection

@section('js')
    <script>
        ajaxUpdateUrl = {!! json_encode(route('admin.permissions.update')) !!}
        ajaxDeleteUrl = {!! json_encode(route('admin.permissions.delete')) !!}
    </script>
@endsection