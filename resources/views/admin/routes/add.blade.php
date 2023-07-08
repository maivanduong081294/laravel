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

@section('content')
<div class="detail">
    <form class="detail-form">
        <div class="detail-main">
            <div class="detail-group">
                <x-form.input label="Tên định tuyến" id="title" name="title" placeholder="true"/>
                <x-form.input label="Đường dẫn" id="uri" name="uri" placeholder="true"/>
            </div>
        </div>
        <div class="detail-sidebar">

        </div>
    </form>
</div>
@endsection