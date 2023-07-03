@if(!empty($breadcrumb))
<ul class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{route('admin')}}">Trang quản trị</a>
    </li>
    @foreach ($breadcrumb as $key=>$item)
    <li class="breadcrumb-item active" aria-current="page">
        @if (count($breadcrumb) > $key + 1)
            <a href="{{$item->link}}">{{$item->title}}</a>
        @else
            {{$item->title}}
        @endif
    </li>
    @endforeach
</ul>
@endif