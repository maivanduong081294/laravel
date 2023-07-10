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

@php
    ob_start();   
@endphp
<tr>
    <th class="text-center"><x-styles.checkbox type="checkbox" input-class="check-list-all"/></th>
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
                    {{-- <div class="data-heading-handle-checkbox handle-checkbox">
                        <x-styles.select class="list-handle-checkbox" default="Hành động" values="{{json_encode($actions)}}"/>
                        <x-styles.button class="btn-handle-checkbox" content="Áp dụng" />
                    </div> --}}
                    {{-- <div class="data-heading-filter">
                        <x-styles.select default="Controllers" name="controller" selected="{{request()->controller}}" key-by-value="1" values="{{json_encode($controllers)}}"/>
                        <x-styles.select default="Methods" name="getMethod" selected="{{request()->getMethod}}" key-by-value="1" values="{{json_encode($methods)}}"/>
                        <x-styles.button type="submit" content="Lọc" />
                    </div> --}}
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
                {{-- <x-styles.select class="list-handle-checkbox" default="Hành động" values="{{json_encode($actions)}}"/>
                <x-styles.button class="btn-handle-checkbox" content="Áp dụng" /> --}}
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
        ajaxUpdateUrl = {!! json_encode(route('admin.routes.update')) !!}
        ajaxDeleteUrl = {!! json_encode(route('admin.routes.delete')) !!}
    </script>
@endsection