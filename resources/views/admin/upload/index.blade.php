@extends('layouts.admin')

@section('title')
    {{$title}}
@endsection

@section('heading')
    {{$heading}}
@endsection

@section('heading-after')
    <x-styles.button href="{{route('admin.routes.add')}}" data-toggle="drag-box-upload-form" content="Thêm mới"/>
@endsection

@section('breadcrumb')
   <x-admin.breadcrumb data="{{json_encode($breadcrumb)}}" />
@endsection

@section('alert')
    @if (session()->get('msg'))
        <x-alert message="{{session()->get('msg')}}" type="{{session()->get('type')}}" />
    @endif
@endsection

@php
    ob_start();   
@endphp
<tr>
    <th class="text-center"><x-styles.checkbox type="checkbox" input-class="check-list-all"/></th>
    <x-styles.th order-by="id" text="Hình ảnh"/>
    <x-styles.th order-by="name" text="Tên"/>
    <x-styles.th order-by="author_id" text="Tác giả"/>
    <x-styles.th order-by="created_at" text="Ngày tải"/>
    <x-styles.th text=""/>
</tr>
@php
    $columns = ob_get_contents();
    ob_end_clean();
@endphp

@section('content')
<div id="drag-box-upload">
    <form id="drag-box-upload-form" action="{{route('admin.upload.add')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="drag-box">
            <div class="drag-box-wrapper">
                <h3>Thả các tập tin để tải lên</h3>
                <div class="drag-box-spacing">Hoặc</div>
                <label for="drag-box-file" class="btn btn-primary">Chọn hình ảnh</label>
            
            </div>
        </div>
    </form>
    <div id="preview"></div>
    <div class="drag-box-main">
        <h3>Thả các tập tin để tải lên</h3>
        <input type="file" id="drag-box-file"/>
    </div>
</div>

<div class="result result-gird">
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
            <input type="hidden" name="orderBy" value="{{request()->orderBy}}">
            <input type="hidden" name="orderType" value="{{request()->orderType}}">
        </form>
    </div>
    <div class="grid-data">
        @if(!empty($list))
            <div class="grid-data-list">
            @foreach($list as $item)
                @php
                    $limit = $item->month_limit==date("m")?1:0;
                @endphp
                <div class="grid-data-item image-upload-item">
                    <div class="grid-data-item-wrapper">
                        <figure class="image-wrapper">
                        <img src="{{getImageSourceById($item->id)}}" data-image="{{getImageSourceById($item->id,'original')}}" alt="{{$item->name}}" />
                        </figure>
                        <div class="image-upload-item-actions">
                            <div class="image-upload-item-actions-list">
                                <x-styles.button href="javascript:void(0)" class="btn-icon btn-view btn-primary" content='<i class="fa-solid fa-eye"></i>' />
                                <x-styles.button href="javascript:void(0)" class="btn-icon btn-link btn-primary" content='<i class="fa-solid fa-link"></i>' />
                                <x-styles.button href="{{route('admin.upload.edit',[$item->id])}}" class="btn-icon btn-edit edit-item-ajax btn-primary" content='<i class="fa-solid fa-pen"></i>' />
                                <x-styles.button href="javascript:void(0)" class="btn-icon btn-delete-data btn-danger" data-item-id="{{$item->id}}" content='<i class="fa-solid fa-trash"></i>' />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div>
    <div class="data-foot">
        @if(!empty($list))
        {{ $list->links() }}
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $(document).on("dragover",'body',function () {
            let dragBox = $('#drag-box-upload .drag-box-main');
            if(dragBox.length > 0) {
                dragBox.addClass("draging");
            }
        });

        $(document).on("drop dragleave",'#drag-box-upload .drag-box-main',function () {
            $(this).removeClass("draging");
        });

        $(document).on("change",'#drag-box-upload input[type="file"]',function () {
            console.log("ok11");
        });

        $(document).on("click",'.image-upload-item-actions-list .btn-view',function () {
            const confirmResult = confirm('Bạn muốn xem hình ảnh gốc?');
            if(confirmResult === true) {
                const image = $(this).parents('.grid-data-item-wrapper').find('img');
                window.open(image.data('image'));
            }
        });
        $(document).on("click",'.image-upload-item-actions-list .btn-link',function () {
            const confirmResult = confirm('Bạn muốn sao chép đường dẫn tới file hình ảnh?');
            if(confirmResult === true) {
                const image = $(this).parents('.grid-data-item-wrapper').find('img');
                $("body").append('<input id="copyURL" type="text" value="" />');
                $("#copyURL").val(image.data('image')).select();
                document.execCommand("copy");
                $("#copyURL").remove();
            }
        });

        $(document).on("click",'.image-edit-actions .btn-link',function () {
            const confirmResult = confirm('Bạn muốn sao chép đường dẫn tới file hình ảnh?');
            if(confirmResult === true) {
                const image = $(this).parents('.edit-upload-wrapper').find('img');
                $("body").append('<input id="copyURL" type="text" value="" />');
                $("#copyURL").val(image.attr('src')).select();
                document.execCommand("copy");
                $("#copyURL").remove();
            }
        });
    });
</script>
<script>
    ajaxUpdateUrl = {!! json_encode(route('admin.upload.update')) !!}
    ajaxDeleteUrl = {!! json_encode(route('admin.upload.delete')) !!}
</script>
<div class="popper popper-fixed" data-popper="edit-item-ajax">
    <div class="popper-fixed-content">
        <div class="popper-fixed-content-header">
            <p>Thay đổi thông tin hình ảnh</p>
            <button class="popper-fixed-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="popper-fixed-content-body">
            
        </div>
    </div>
</div>
@endsection



@section('css')
<style>
    .file-information td:nth-child(1) {
        font-weight: bold;
        padding-right: 10px;
    }
    .image-edit-actions {
        margin-top: 20px;
    }
    .edit-upload-right {
        margin-top: 20px;
    }
    .image-update-fields {
        margin-top: 20px;
    }
    @media (min-width: 768px) {
        .edit-upload-left {
            position: relative;
        }
        .edit-upload-right {
            margin-top: 0px;
        }
        .edit-upload-left figure.image-wrapper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }
    }
</style>
@endsection