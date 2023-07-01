@php
    use App\Models\User;
    $user = User::getCurrentUser();

    $notifications = [1]
@endphp
<header id="header">
    <a href="javascript:void(0)" id="menu-toggle" class="header-button"><i class="fa-solid fa-bars"></i></a>
    <a href="{{route('admin')}}" id="logo" class="header-button"><img src="{{getLogo()}}" alt="Logo"/></a>
    <div class="header-actions">
        <div class="header-notifications">
            <a href="javascript:void(0)" class="header-button popper-button" data-popper="notifications"><i class="fa-solid fa-bell"></i></a>
            <div class="popper popper-header popper-notifications" data-popper="notifications">
                @if (empty($notifications))
                <div class="popper-notifications-empty">
                    <p>Không có thông báo</p>
                </div>
                @else
                <div class="popper-notifications-header">Bạn có 4 tin nhắn mới</div>
                <div class="popper-notifications-body">
                    <a href="#" class="notifications-item">
                        <div class="notifications-item-icon btn-danger"><i class="fa-regular fa-comment"></i></div>
                        <div class="notifications-item-content">
                            <div class="notifications-item-title">Bình luận</div>
                            <div class="notifications-item-excerpt text-ellipsis">Có một bình luận mới về sản phẩm 123</div>
                            <div class="notifications-item-time">{{date("d/m/Y H:i:s")}}</div>
                        </div>
                    </a>
                    <a href="#" class="notifications-item">
                        <div class="notifications-item-icon btn-success"><i class="fa-regular fa-comment"></i></div>
                        <div class="notifications-item-content">
                            <div class="notifications-item-title">Bình luận</div>
                            <div class="notifications-item-excerpt text-ellipsis">Có một bình luận mới về sản phẩm 123</div>
                            <div class="notifications-item-time">{{date("d/m/Y H:i:s")}}</div>
                        </div>
                    </a>
                    <a href="#" class="notifications-item">
                        <div class="notifications-item-icon btn-warning"><i class="fa-regular fa-comment"></i></div>
                        <div class="notifications-item-content">
                            <div class="notifications-item-title">Bình luận</div>
                            <div class="notifications-item-excerpt text-ellipsis">Có một bình luận mới về sản phẩm 123</div>
                            <div class="notifications-item-time">{{date("d/m/Y H:i:s")}}</div>
                        </div>
                    </a>
                    <a href="#" class="notifications-item">
                        <div class="notifications-item-icon btn-primary"><i class="fa-regular fa-comment"></i></div>
                        <div class="notifications-item-content">
                            <div class="notifications-item-title">Bình luận</div>
                            <div class="notifications-item-excerpt text-ellipsis">Có một bình luận mới về sản phẩm 123</div>
                            <div class="notifications-item-time">{{date("d/m/Y H:i:s")}}</div>
                        </div>
                    </a>
                </div>
                <a href="#" class="popper-notifications-footer">Xem tất cả thông báo</a>
                @endif
                
            </div>
        </div>
        <div class="header-profile">
            <div class="header-profile-top">
                <a id="header-avatar" href="javascript:void(0)" class="header-button popper-button" data-popper="user-menu">
                    <figure class="image">
                        <img src="{{$user->avatar}}" alt="{{$user->fullname}}">
                    </figure>
                </a>
                <div class="popper popper-header popper-user-menu" data-popper="user-menu">
                    <div class="popper-user-menu-header">
                        <div class="popper-user-menu-avatar">
                            <figure class="image">
                                <img src="{{$user->avatar}}" alt="{{$user->fullname}}">
                            </figure>
                        </div>
                        <div class="popper-user-menu-content">
                            <div class="popper-user-menu-fullname text-ellipsis">{{$user->fullname}}</div>
                            <div class="popper-user-menu-email text-ellipsis">{{$user->email}}</div>
                        </div>
                    </div>
                    <div class="popper-user-menu-body">
                        <div class="user-menu-item">
                            <a href="#">
                                <div class="user-menu-icon text-primary"><i class="fa-regular fa-user"></i></div>
                                <div class="user-menu-text">Thông tin cá nhân</div>
                            </a>
                        </div>
                        <div class="user-menu-item">
                            <a href="#">
                                <div class="user-menu-icon text-warning"><i class="fa-regular fa-envelope"></i></div>
                                <div class="user-menu-text">Hộp thư</div>
                            </a>
                        </div>
                        <div class="user-menu-item">
                            <a href="{{route('logout')}}">
                                <div class="user-menu-icon text-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i></div>
                                <div class="user-menu-text">Đăng xuất</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>