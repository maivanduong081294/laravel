<aside id="sidebar">
    <div class="sidebar-wrapper">
        <ul class="menu-list">
            <li class="menu-item">
                <a href="{{route('admin')}}">
                    <span class="menu-icon"><i class="fa-solid fa-house"></i></span>
                    <span class="menu-text">Bảng điều khiển</span>
                </a>
            </li>
            <li class="menu-item has-children">
                <a href="{{route('admin.users.index')}}">
                    <span class="menu-icon"><i class="fa-solid fa-user"></i></span>
                    <span class="menu-text">Tài khoản</span>
                </a>
                <ol class="menu-children">
                    <li class="menu-child-item">
                        <a href="{{route('admin.users.index')}}">
                            <span class="menu-text">Danh sách</span>
                        </a>
                    </li>
                    <li class="menu-child-item">
                        <a href="{{route('admin')}}">
                            <span class="menu-text">Bảng điều khiển</span>
                        </a>
                    </li>
                    <li class="menu-child-item">
                        <a href="{{route('admin')}}">
                            <span class="menu-text">Bảng điều khiển</span>
                        </a>
                    </li>
                </ol>
            </li>
            <li class="menu-item has-children">
                <a href="{{route('admin')}}">
                    <span class="menu-icon"><i class="fa-solid fa-user"></i></span>
                    <span class="menu-text">Tài khoản</span>
                </a>
                <ol class="menu-children">
                    <li class="menu-child-item">
                        <a href="{{route('admin')}}">
                            <span class="menu-text">Bảng điều khiển</span>
                        </a>
                    </li>
                </ol>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="#"><i class="fa-solid fa-gear"></i></a>
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
            <a href="{{route('logout')}}"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
        </div>
    </div>
</aside>