@extends('common.layouts')

@section('style')
{{--    <style type="text/css">--}}
{{--        .sidebar-menu {--}}
{{--            overflow: auto !important;--}}
{{--            max-height: calc(100vh - 50px);--}}
{{--        }--}}
{{--    </style>--}}
@endsection

@section('content')
    <div class="wrapper">
        <!-- Main Header -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index3.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Contact</a>
                </li>
            </ul>
            <!-- SEARCH FORM -->
            <form class="form-inline ml-3">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <!-- Header Navbar -->
            <ul class="navbar navbar-static-top navbar-nav ml-auto" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->

                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img src="{{show_user_image($admin->wxAccount->headimgurl ?? '')}}" class="user-image"
                                     alt="User Image">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">{{$admin->showName($admin->id)}}</span>
                                <i class="fa fa-fw fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img src="{{show_user_image($admin->wxAccount->headimgurl ?? '')}}" class="img-circle"
                                         alt="User Image">

                                    <p>
                                        {{$admin->showName($admin->id)}}
                                        <small>加盟日期：{{$admin->created_at->format('Y-m-d')}}</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="row">
                                        <div class="col-xs-4 text-center">
                                            <a target="content-iframe" href="{{route('personals.show')}}">账号管理</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a target="content-iframe" href="{{route('personals.password')}}">修改密码</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a target="content-iframe" href="{{route('logs.index')}}">操作日志</a>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{url('admin-lockscreen')}}" class="btn btn-default btn-flat">锁屏</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{url('admin-logout')}}" class="btn btn-default btn-flat">退出</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </ul>
        </nav>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="{{asset('admin-ui/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">AdminLTE 3</span>
            </a>
            <!-- sidebar: style can be found in sidebar.less -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul id="menu-bar" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        @foreach ($menus as $menu)
                            @if(isset($menu->child) && count($menu->child))
                                <li class="nav-item treeview">
                                    <a class="nav-link" href="#">
                                        <i class="{{$menu->icon}}"></i>
                                        <p>{{$menu->title}}
                                            <i class="fas fa-angle-left right"></i>
                                            <span id="{{$menu->auth_name}}" class="badge badge-info right hide">0</span>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach ($menu->child as $child2)
                                            @if(isset($child2->child) && count($child2->child) > 0)
                                                <li class="nav-item treeview">
                                                    <a class="nav-link" href="#">
                                                        <i class="fa fa-angle-left pull-right"></i>
                                                        <p>{{$child2->title}}</p>
                                                    </a>
                                                    <ul class="nav nav-treeview">
                                                        @foreach ($child2->child as $child3)
                                                            <li><a class="nav-link" target="content-iframe" href="{{url($child3->route_url)}}"><p>{{$child3->title}}</p></a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @else
                                                <li class="nav-item"><a class="nav-link" target="content-iframe" href="{{url($child2->route_url)}}"><i
                                                            class="{{$child2->icon}}"></i> <p
                                                        >{{$child2->title}}
                                                            <span id="{{$menu->auth_name}}" class="badge badge-info right hide">0</span>
                                                        </p>

                                                    </a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>

                            @else
                                <li class="nav-item"><a class="nav-link" target="content-iframe" href="{{url($menu->route_url)}}"><i
                                            class="{{$menu->icon}}"></i> <p
                                        >{{$menu->title}}</p></a></li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <iframe name="content-iframe" id="content-iframe" src="{{url('admin-console')}}" frameborder="0"></iframe>
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="pull-right hidden-xs">
                Version {{get_version()}}
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; <?= date ('Y') ?> <a href="#">{{get_config_value('system_company_name','')}}</a>.</strong> All rights reserved.
        </footer>

        <div class="control-sidebar-bg"></div>
        <div id="pay_new_order" style="display:none">

        </div>

    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            $("body").css('overflow', 'hidden')
            // get_sync_tips_number();
        })

        function get_sync_tips_number() {
            $.ajax({
                type: 'post',
                url: '{{route('admin-main-tips')}}',
                data: [],
                dataType: 'json',
                error: function () {

                },
                complete: function () {
                    setTimeout(function () {
                        get_sync_tips_number();
                    }, 10000);
                },
                success: function (json) {
                    if (json.error !== true) {
                        result = json.result
                        if (result.order_menu !== undefined) {
                            $("#order_menu").text(result.order_menu)
                            if (result.order_menu > 0) {
                                $("#order_menu").removeClass('hide')
                            } else {
                                $("#order_menu").addClass('hide')
                            }
                        }
                        if (result.orderPending !== undefined) {
                            $("#orderPending small").text(result.orderPending)
                            if (result.orderPending > 0) {
                                $("#orderPending").removeClass('hide')
                            } else {
                                $("#orderPending").addClass('hide')
                            }
                        }
                        if (result.orderSales !== undefined) {
                            $("#orderSales small").text(result.orderSales)
                            if (result.orderSales > 0) {
                                $("#orderSales").removeClass('hide')
                            } else {
                                $("#orderSales").addClass('hide')
                            }
                        }
                        if (result.has_new_order !== undefined) {
                            if (result.has_new_order > 0) {
                                //有新订单，播放提醒音频

                                $("#pay_new_order").html('<audio controls="controls" autoplay="autoplay">\n' +
                                    '                <source src="{{asset('admin-ui/tips.mp3')}}" type="audio/mpeg" />\n' +
                                    '                Your browser does not support the audio element.\n' +
                                    '            </audio>');
                            }
                        }
                    }
                }
            })
        }
    </script>
@endsection
