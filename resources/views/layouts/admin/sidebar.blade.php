@php
    $permissionController = getAdminController('Permissions');
    $menu = $permissionController->menu();
@endphp
<aside id="sidebar">
    <div class="sidebar-wrapper">
        <ul class="menu-list">
            @foreach ($menu as $item)
                <li class="menu-item{{!empty($item->children) ? ' has-children' : ''}}">
                    <a href="{{$item->link}}">
                        <span class="menu-icon">{!!$item->icon!!}</span>
                        <span class="menu-text">{{$item->name}}</span>
                    </a>
                    @if (!empty($item->children))
                    <ol class="menu-children">
                        @foreach ($item->children as $subitem)
                            <li class="menu-child-item">
                                <a href="{{$subitem->link}}">
                                    <span class="menu-text">
                                        {{$subitem->name}}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="sidebar-footer">
            <a href="#"><i class="fa-solid fa-gear"></i></a>
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
            <a href="{{route('logout')}}"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
        </div>
    </div>
</aside>