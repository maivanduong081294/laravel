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
<div class="table-data">
    <table class="table">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="checkall"/></th>
                <th scope="col">Title</th>
                <th scope="col">Uri</th>
                <th scope="col">Controller</th>
                <th scope="col">Function</th>
                <th scope="col">Methods</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @if(empty($list))
            <tr>
                <th scope="row" colspan="7">Không có dữ liệu</th>
            </tr>
            @else
            @foreach($list as $item)
                <tr>
                    <th scope="row"><input type="checkbox" class="check" value="{{$item->id}}"/></th>
                    <td>{{$item->title}}</td>
                    <td>{{$item->uri}}</td>
                    <td>{{$item->controller}}</td>
                    <td>{{$item->function}}</td>
                    <td>{{$item->method}}</td>
                    <td>{{$item->status}}</td>
                </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    {{ $list->links() }}
</div>
@endsection
