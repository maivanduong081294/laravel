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
<form action="{{route('admin.upload.add')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">

        <div class="col-md-6">
            <input type="file" name="image" class="form-control">
            <input type="text" name="name" placeholder="name">
            <input type="text" name="type" placeholder="type">
            @error('image')
                {{$message}}
            @enderror
        </div>

        <div class="col-md-6">
            <button type="submit" class="btn btn-success">Upload</button>
        </div>

    </div>
</form>
@endsection