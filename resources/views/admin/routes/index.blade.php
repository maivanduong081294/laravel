@extends('layouts.admin')

@section('title')
    {{$title}}
@endsection

@section('heading')
    {{$heading}}
@endsection

@section('heading-after')
    <x-styles.button href="{{route('admin.routes.add')}}" content="Thêm mới"/>
@endsection

@section('breadcrumb')
   <x-admin.breadcrumb data="{{json_encode($breadcrumb)}}" />
@endsection

@section('content')
<div class="result">
    <div class="data-heading">
        <form action="">
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
                    <div class="data-heading-handle-checkbox">
                        <x-styles.select default="Hành động" values="{{json_encode($actions)}}"/>
                        <x-styles.button id="heading-action" content="Áp dụng" />
                    </div>
                    <div class="data-heading-filter">
                        <x-styles.select default="Controllers" name="controller" selected="{{request()->controller}}" key-by-value="1" values="{{json_encode($controllers)}}"/>
                        <x-styles.select default="Methods" name="getMethod" selected="{{request()->getMethod}}" key-by-value="1" values="{{json_encode($methods)}}"/>
                        <x-styles.button type="submit" content="Lọc" />
                    </div>
                </div>
                {{ $list->links() }}
            </div>
        </form>
    </div>
    <div class="table-data">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center"><x-styles.checkbox type="checkbox" input-class="check-list-all"/></th>
                    <th>Title</th>
                    <th>Uri</th>
                    <th>Controller</th>
                    <th>Function</th>
                    <th>Methods</th>
                    <th>Middleware</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if($list->count()<=0)
                <tr>
                    <th colspan="8">Không có dữ liệu</th>
                </tr>
                @else
                @foreach($list as $item)
                    <tr>
                        <td class="text-center" data-title="Chọn"><x-styles.checkbox type="checkbox" class="check" input-class="check-list-item" value="{{$item->id}}"/></th>
                        <td data-title="Title">
                            <a href="{{route('admin.routes.edit',['id'=>$item->id])}}">{{$item->title}}</a>
                        </td>
                        <td data-title="Uri">{{$item->uri}}</td>
                        <td data-title="Controller">{{$item->controller}}</td>
                        <td data-title="Function">{{$item->function}}</td>
                        <td data-title="Methods">{{$item->method}}</td>
                        <td data-title="Middleware">{{$item->middleware}}</td>
                        <td data-title="Status"><x-styles.status class="main" value="{{$item->status}}" /></td>
                    </tr>
                @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center"><x-styles.checkbox type="checkbox" input-class="check-list-all"/></th>
                    <th>Title</th>
                    <th>Uri</th>
                    <th>Controller</th>
                    <th>Function</th>
                    <th>Methods</th>
                    <th>Middleware</th>
                    <th>Status</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="data-foot">
        <div class="data-foot-actions">
            <div class="data-foot-handle-checkbox">
                <x-styles.select default="Hành động" values="{{json_encode($actions)}}"/>
                <x-styles.button id="heading-action" content="Áp dụng" />
            </div>
        </div>
        {{ $list->links() }}
    </div>
</div>
@endsection