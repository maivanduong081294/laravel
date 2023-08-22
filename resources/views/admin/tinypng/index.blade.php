@extends('layouts.admin')

@section('title')
    {{$title}}
@endsection

@section('heading')
    {{$heading}}
@endsection

@section('heading-after')
    <x-styles.button data-popper="add-new-item" class="add-new-item popper-button" content="Thêm mới"/>
    <div class="popper popper-fixed" data-popper="add-new-item">
        <div class="popper-fixed-content">
            <div class="popper-fixed-content-header">
                <p>Thêm mới API Tiny PNG</p>
                <button class="popper-fixed-close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="popper-fixed-content-body">
                <form id="add-new-item-form" enctype="multipart/form-data">
                    <x-form.input value="" name="api" label="API" placeholder="API" />
                    <x-styles.button content="Thêm mới"/>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('breadcrumb')
   <x-admin.breadcrumb data="{{json_encode($breadcrumb)}}" />
@endsection

@php
    ob_start();   
@endphp
<tr>
    <th class="text-center"><x-styles.checkbox type="checkbox" input-class="check-list-all"/></th>
    <x-styles.th order-by="API" text="API"/>
    <x-styles.th order-by="count" text="Đã gọi"/>
    <x-styles.th order-by="month_limit" text="Giới hạn"/>
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
                        <x-styles.select default="Trạng thái" name="status" selected="{{request()->status}}" values="{{json_encode($listStatus)}}"/>
                        <x-styles.select default="Giới hạn" name="limit" selected="{{request()->limit}}" values="{{json_encode($listLimit)}}"/>
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
                    @php
                        $limit = $item->month_limit==date("m")?1:0;
                    @endphp
                    <tr>
                        <td class="min-col" class="text-center" data-title="Chọn"><x-styles.checkbox type="checkbox" class="check" name="id" input-class="check-list-item" value="{{$item->id}}"/></th>
                        <td data-title="API">
                            {{$item->api}}
                        </td>
                        <td data-title="Đã gọi">{{$item->count}} (lần)</td>
                        <td data-title="Giới hạn" class="min-col"><x-styles.status value="{{$limit}}" name="limit" /></td>
                        <td data-title="Trạng thái" class="min-col"><x-styles.status value="{{$item->status}}" name="status" update="{{$item->status?0:1}}"/></td>
                        <td class="text-center">
                            <x-styles.button href="{{route('admin.tinypng.edit',$item->id)}}" class="btn-warning edit-item-ajax" content='<i class="fa-solid fa-pen"></i>'/>
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

@section('css')
<style>
    .popper-fixed-content {
        max-width: 400px;
    }
</style>
@endsection

@section('js')
    <script>
        ajaxInsertUrl = {!! json_encode(route('admin.tinypng.add')) !!}
        ajaxUpdateUrl = {!! json_encode(route('admin.tinypng.update')) !!}
        ajaxDeleteUrl = {!! json_encode(route('admin.tinypng.delete')) !!}
    </script>
    <div class="popper popper-fixed" data-popper="edit-item-ajax">
        <div class="popper-fixed-content">
            <div class="popper-fixed-content-header">
                <p>Thay đổi API Tiny PNG</p>
                <button class="popper-fixed-close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="popper-fixed-content-body">
                
            </div>
        </div>
    </div>
@endsection