<aside class="app-aside app-aside-expand-md app-aside-light">
    <!-- .aside-content -->
    <div class="aside-content">
        <!-- .aside-header -->
        <header class="aside-header d-block d-md-none">
            <!-- .btn-account -->
            <button class="btn-account" type="button" data-toggle="collapse" data-target="#dropdown-aside"><span class="user-avatar user-avatar-lg"><img src="{{ auth()->user()->avatar }}" alt=""></span> <span class="account-icon"><span class="fa fa-caret-down fa-lg"></span></span> <span class="account-summary"><span class="account-name">{{ auth()->user()->name }}</span> <span class="account-description">dsf</span></span></button> <!-- /.btn-account -->
            <!-- .dropdown-aside -->
            <div id="dropdown-aside" class="dropdown-aside collapse">
                <!-- dropdown-items -->
                <div class="pb-3">
                    <a class="dropdown-item" href="{{ route('admin.profile') }}"><span class="dropdown-icon oi oi-person"></span> Profile</a>
                    <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault();document.getElementById('logout_form')">
                        <span class="dropdown-icon oi oi-account-logout"></span> Logout</a>
                    <form action="{{ route('admin.logout') }}" id="logout_form" method="POST">@csrf</form>
                    <div class="dropdown-divider"></div><a class="dropdown-item" href="#">Help Center</a> <a class="dropdown-item" href="#">Ask Forum</a> <a class="dropdown-item" href="#">Keyboard Shortcuts</a>
                </div><!-- /dropdown-items -->
            </div><!-- /.dropdown-aside -->
        </header><!-- /.aside-header -->
        <!-- .aside-menu -->
        <div class="aside-menu overflow-hidden">
            <!-- .stacked-menu -->
            <nav id="stacked-menu" class="stacked-menu">
                <!-- .menu -->
                <ul class="menu">

                    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'has-active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link"><span class="menu-icon fas fa-home"></span> <span class="menu-text">Dashboard</span></a>
                    </li>


                    <li class="menu-header">Blog Management</li>
                    <li class="menu-item has-child {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.polls.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') ? 'has-active' : '' }}">
                        <a href="#" class="menu-link"><span class="menu-icon oi oi-document"></span> <span class="menu-text">Posts</span></a>
                        <ul class="menu">
                            @can('post.view')
                                <li class="menu-item {{ request()->routeIs('admin.posts.*') ? 'has-active' : '' }}">
                                    <a href="{{ route('admin.posts.index') }}" class="menu-link"><span class="menu-text">All Posts</span></a>
                                </li>
                            @endcan

                            @can('category.view')
                                <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'has-active' : '' }}">
                                    <a href="{{ route('admin.categories.index') }}" class="menu-link"><span class="menu-text">Categories</span></a>
                                </li>
                            @endcan

                            @can('subcategory.view')
                                <li class="menu-item {{ request()->routeIs('admin.subcategories.*') ? 'has-active' : '' }}">
                                    <a href="{{ route('admin.subcategories.index') }}" class="menu-link"><span class="menu-text">Sub Categories</span></a>
                                </li>
                            @endcan

                            @can('poll.view')
                                <li class="menu-item {{ request()->routeIs('admin.polls.*') ? 'has-active' : '' }}">
                                    <a href="{{ route('admin.polls.index') }}" class="menu-link"><span class="menu-text">Opinion Polls</span></a>
                                </li>
                            @endcan

                        </ul>
                    </li>

                    <li class="menu-item has-child {{ request()->routeIs('admin.profile') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ||  request()->routeIs('admin.permissions.*')? 'has-active' : '' }}">
                        <a href="#" class="menu-link"><span class="menu-icon oi oi-person"></span> <span class="menu-text">User</span></a>
                        <ul class="menu">
                            <li class="menu-item {{ request()->routeIs('admin.profile') ? 'has-active' : '' }}">
                                <a href="{{ route('admin.profile') }}" class="menu-link">Profile</a>
                            </li>
                            @can('role.view')
                            <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'has-active' : '' }}">
                                <a href="{{ route('admin.roles.index') }}" class="menu-link">Roles &amp; Permissions</a>
                            </li>
                            @endcan
                            @can('permission.view')
                            <li class="menu-item {{ request()->routeIs('admin.permissions.*') ? 'has-active' : '' }}">
                                <a href="{{ route('admin.permissions.index') }}" class="menu-link">Permissions</a>
                            </li>
                            @endcan

                            @can('user.view')
                            <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'has-active' : '' }}">
                                <a href="{{ route('admin.users.index') }}" class="menu-link">User Management</a>
                            </li>
                            @endcan
                        </ul>
                    </li>

                    @can('setting.view')
                    <li class="menu-item has-child {{ request()->routeIs('admin.settings*') ? 'has-active' : '' }}">
                        <a href="#" class="menu-link"><span class="menu-icon oi oi-wrench"></span> <span class="menu-text">Setting</span></a>
                        <ul class="menu">
                            <li class="menu-item {{ request()->routeIs('admin.settings') ? 'has-active' : '' }}">
                                <a href="{{ route('admin.settings') }}" class="menu-link">General Setting</a>
                            </li>
                        </ul>
                    </li>
                    @endcan


                </ul><!-- /.menu -->
            </nav><!-- /.stacked-menu -->
        </div><!-- /.aside-menu -->
        <!-- Skin changer -->
        <footer class="aside-footer border-top p-2">
            <button class="btn btn-light btn-block text-primary" data-toggle="skin"><span class="d-compact-menu-none">Night mode</span> <i class="fas fa-moon ml-1"></i></button>
        </footer><!-- /Skin changer -->
    </div><!-- /.aside-content -->
</aside>
