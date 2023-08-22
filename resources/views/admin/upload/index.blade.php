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
        <input type="file" name="myfile" id="drag-box-file"/>
    </div>
</div>

<div class="result result-gird">
    <div class="data-heading">
        <form action="" id="data-form">
            <div class="data-heading-top">
                <div class="data-heading-show-number">
                    <span>Hiển thị</span>
                    <x-styles.select key-by-value="1" id="set-show-items-number" selected="{{$perPage}}" values="{{listShowItemsNumber(true)}}"/>
                    @if(!empty($list))
                        <x-styles.button type="button" id="select-images" content="Chọn nhiều"/>
                    @endif
                </div>
                <div class="search-data">
                    <x-styles.input type="text" name="search" value="{{request()->search}}" placeholder="Nhập từ khoá tìm kiếm" />
                    <x-styles.button type="submit" content="Tìm kiếm"/>
                </div>
            </div>
            <input type="hidden" name="orderBy" value="{{request()->orderBy}}">
            <input type="hidden" name="orderType" value="{{request()->orderType}}">
        </form>
        <div id="remove-actions">
            <x-styles.button type="button" class="btn-danger" id="delete-images" content="Xoá hình ảnh" disabled="true"/>
            <x-styles.button type="button" id="select-all-images" content="Chọn tất cả"/>
            <x-styles.button type="button" id="cancel-images" content="Huỷ" />
        </div>
    </div>
    <div class="grid-data">
        <div class="grid-data-list">
        @if(!empty($list))
            @foreach($list as $item)
                @php
                    $limit = $item->month_limit==date("m")?1:0;
                @endphp
                @include('admin.upload.item')
            @endforeach
        @endif
        </div>
    </div>
    <div class="data-foot">
        @if(!empty($list))
        {{ $list->links() }}
        @endif
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
<script>
    $(document).ready(function() {
        let r = new Resumable({
            target: {!! json_encode(route('admin.upload.add')) !!},
            query: {_token: ajaxToken},
            testChunks: false,
            forceChunkSize: true,
            chunkSize: (3 * 1024 * 1024),
        });

        let uploaded = false;

        var results = $('.grid-data-list'),
        browseButton = $('#drag-box-upload input[type="file"]');

        if(!r.support) {
            console.log('no support');
        }

        r.assignBrowse(browseButton);

        r.on('fileAdded', function (file, event) {
            uploaded = false;
            let dragBox = $('#drag-box-upload .drag-box-main');
            if(dragBox.length > 0) {
                dragBox.removeClass("draging");
            }
            var template = '<div class="grid-data-item image-upload-item" data-uniqueId="'+file.uniqueIdentifier+'">'+
                                '<div class="grid-data-item-wrapper">'+
                                    '<figure class="image-wrapper"><div class="image-upload-process"><i></i><span></span></div></figure>'+
                            '</div>'+
                            '</div>';

            results.prepend(template);
            r.upload();
            $('#drag-box-upload input[type="file"]').val("");
        });

        r.on('fileProgress', function (file) {
            let item = $('.image-upload-item[data-uniqueId=' + file.uniqueIdentifier + ']');
            var progress = Math.floor(file.progress() * 100);
            item.find('.image-upload-process i').css('width', progress + '%');
            item.find('.image-upload-process span').html(progress + '%');
        });

        r.on('fileSuccess', function (file, message) {
            uploaded = true; 
            let item = $('.image-upload-item[data-uniqueId=' + file.uniqueIdentifier + ']');
            if(item.length > 0) {
                $(message).insertBefore(item);
                item.remove();
            }
            r.removeFile(file);
        });

        r.on('fileError', function(file, response){
            response = JSON.parse(response);
            let error = response.error;
            let item = $('.image-upload-item[data-uniqueId=' + file.uniqueIdentifier + ']');
            if(item.length > 0) {
                item.find('.image-wrapper').html('<span class="error-text">'+error+'</span>');
            }
            r.removeFile(file);
        });

        r.on('uploadStart', function () {
            $('#content > .alert').remove();
            $('#content').prepend(`<x-alert message="Đang upload hình ảnh" type="warning" />`);
        });

        r.on('complete', function () {
            $('#content > .alert').remove();
            if(uploaded) {
                $('#content').prepend(`<x-alert message="Upload hình ảnh hoàn tất" type="success" />`);
            }
            else {
                $('#content').prepend(`<x-alert message="Upload hình ảnh thất bại"  />`);
            }
        });

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
            $(this).val("");
            $('#drag-box-upload .drag-box-main').removeClass("draging");
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

        $(document).on("click","#select-images", function(e) {
            e.preventDefault();
            $("#remove-actions").show();
            $("#data-form").hide();
            $(".image-upload-item").addClass("enabled-selected");
        });

        $(document).on("click","#cancel-images", function(e) {
            e.preventDefault();
            $("#remove-actions").hide();
            $("#data-form").show();
            $(".image-upload-item").removeClass("enabled-selected is-selected");
        });

        $(document).on("click","#select-all-images", function(e) {
            e.preventDefault();
            if($(".image-upload-item.enabled-selected:not(.is-selected)").length > 0) {
                $(".image-upload-item.enabled-selected").addClass("is-selected");
                $("#delete-images").prop("disabled",false);
            }
            else {
                $(".image-upload-item.enabled-selected").removeClass("is-selected");
                $("#delete-images").prop("disabled",true);
            }
        });

        $(document).on("click","#delete-images", function(e) {
            e.preventDefault();
            let $this = $(this);
            let ids = [];
            $(".image-upload-item.enabled-selected.is-selected").each(function() {
                ids.push($(this).find(".btn-delete-data").data("item-id"));
            })
            $this.prop('disabled',true);
            $.ajax({
                url: ajaxDeleteUrl,
                type: 'DELETE',
                data: {
                    ids: ids,
                    _token: ajaxToken,
                },
                success: function(data) {
                    $this.prop('disabled',false);
                    alert('Thành công!');
                    location.reload();
                },
                error: function(response, status, error) {
                    $this.prop('disabled',false);
                    if(response.responseJSON.message) {
                        alert(response.responseJSON.message);
                    }
                    else {
                        alert('Lỗi! Vui lòng thử lại sau!');
                    }
                }
            });
        });

        $(document).on("click",".image-upload-item.enabled-selected", function(e) {
            e.preventDefault();
            $(this).toggleClass("is-selected");
            if($(".image-upload-item.enabled-selected.is-selected").length > 0) {
                $("#delete-images").prop("disabled",false);
            }
            else {
                $("#delete-images").prop("disabled",true);
            }
        });
    });
</script>
<script>
    ajaxInsertUrl = {!! json_encode(route('admin.upload.add')) !!}
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
    #drag-box-upload-form {
        margin-bottom: 20px;
    }
    .edit-upload-left {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .edit-upload-left figure {
        flex: 1 1 100%;
        text-align: center;
    }
    .edit-upload-left figure img {
        box-shadow: inset 0 0 15px rgba(0,0,0,.1), inset 0 0 0 1px rgba(0,0,0,.05);
        background: #f0f0f1;
    }
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
    .image-upload-process {
        position: absolute;
        width: calc(100% - 10px);
        height: 10px;
        top: 50%;
        left: 5px;
        border-radius: 5px;
        background: #ccc;
        text-align: center;
    }
    .image-upload-process i {
        position: absolute;
        height: 10px;
        background: var(--primary-color);
        width: 0;
        top: 0;
        left: 0;
        border-radius: 5px;
    }
    .image-upload-process span {
        top: 12px;
        position: relative;
    }
    .image-wrapper .error-text {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        display: flex;
        align-items: center;
        padding: 5px;
        color: var(--btn-danger-color);
    }
    .image-upload-item-actions {
        display: flex;
        visibility: hidden;
        align-items: center;
        justify-content: center;
        background: rgb(83 123 53 / 30%);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .image-upload-item-actions-list {
        flex: 0 0 76px;
        display: flex;
        flex-wrap: wrap;
    }
    .image-upload-item-actions .btn-icon {
        border-radius: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 32px;
        max-width: 32px;
        min-width: auto;
        height: 32px;
        min-height: auto;
        font-size: 12px;
        margin: 3px;
        transition: transform 1s ease;
        text-decoration: none;
        padding: 0;
    }
    .image-upload-item-actions .btn-icon:hover {
        background: #fff;
    }
    .image-upload-item-actions a:nth-child(1) {
        transform: translate(-5px,-5px);
    }
    .image-upload-item-actions a:nth-child(2) {
        transform: translate(5px,-5px);
    }
    .image-upload-item-actions a:nth-child(3) {
        transform: translate(-5px,5px);
    }
    .image-upload-item-actions a:nth-child(4) {
        transform: translate(5px,5px);
    }
    .image-upload-item:not(.enabled-selected) .grid-data-item-wrapper:hover .image-upload-item-actions {
        visibility: visible;
    }
    .image-upload-item.enabled-selected .grid-data-item-wrapper {
        opacity: 0.7;
        cursor: pointer;
    }
    .image-upload-item.enabled-selected.is-selected .grid-data-item-wrapper {
        opacity: 1;
    }
    .image-upload-item.enabled-selected.is-selected .grid-data-item-wrapper::before {
        content: "\f058";
        font-family: 'FontAwesome';
        position: absolute;
        right: 5px;
        bottom: 5px;
        z-index: 2;
        font-size: 31px;
        line-height: 1;
        color: var(--primary-color);
        background: #fff;
        border-radius: 100%;
        overflow: hidden;
        width: 30px;
        height: 30px;
        text-align: center;
    }
    .grid-data-item-wrapper:hover .image-upload-item-actions a {
        transform: translate(0,0);
    }
    div#remove-actions {
        background: #fff;
        padding: 10px;
        display: none;
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