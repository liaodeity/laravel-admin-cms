<!DOCTYPE HTML>
<html>
<head>

    <meta charset="utf-8">
    <title> @if(request()->is('agent*')){{get_config_value('agent_sys_title','代理商管理平台')}}@else后台管理系统@endif - {{get_config_value('system_company_name','')}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{mix_build_dist('/css/admin/vendor.css')}}">
    <link rel="stylesheet" href="{{mix_build_dist('/css/admin/app.css')}}">
{{--    <link href="{{asset ('admin-ui/lib/umeditor/themes/default/css/umeditor.css')}}" type="text/css" rel="stylesheet">--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/bootstrap/dist/css/bootstrap.min.css')}}">--}}
{{--    <!-- Font Awesome -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/font-awesome/css/font-awesome.min.css')}}">--}}
{{--    <!-- Ionicons -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/Ionicons/css/ionicons.min.css')}}">--}}
{{--    <!-- DataTables -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/zTree/v3/css/zTreeStyle/zTreeStyle.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/iColor/iColor-min.css')}}">--}}
{{--    <!--上传附件-->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/webuploader/webuploader.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('admin-ui/lib/address-picker/dist/css/address-picker.css')}}">--}}
{{--    <!-- Theme style -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/dist/css/AdminLTE.min.css')}}">--}}
{{--    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter--}}
{{--          page. However, you can choose any other skin. Make sure you--}}
{{--          apply the skin class to the body tag so the changes take effect. -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/dist/css/skins/skin-blue-light.min.css')}}">--}}
{{--    <!-- bootstrap slider -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/lib/seiyria-bootstrap-slider/css/bootstrap-slider.min.css')}}">--}}

{{--    <!--  Admin  Style -->--}}
{{--    <link rel="stylesheet" href="{{asset ('admin-ui/css/styles.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('admin-ui/css/admin.css?t='.get_version())}}">--}}
{{--    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->--}}
{{--    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->--}}
{{--    <!--[if lt IE 9]>--}}
{{--    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>--}}
{{--    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>--}}
{{--    <![endif]-->--}}
{{--    <script src="{{asset ('admin-ui/lib/jquery/dist/jquery.min.js')}}"></script>--}}
    <script>
        var SYS_TOKEN = '{{csrf_token()}}'
    </script>
    @yield('style')
</head>
<body class="hold-transition layout-top-nav @yield('body_class') ">
<!-- 主体 -->
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md border-bottom-0 navbar-dark navbar-primary ">
        <div class="container-fluid">
            <a href="../../index3.html" class="navbar-brand">
                <img src="{{asset('admin-ui/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">AdminLTE 3</span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    @inject("menuRepositoryEloquent", "App\Repositories\MenuRepositoryEloquent")

                    @foreach ($menuRepositoryEloquent->getMenuList () as $menu)
                        @if(isset($menu->child) && count($menu->child))
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu{{$menu->id}}" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">{{$menu->title}}</a>
                                <ul aria-labelledby="dropdownSubMenu{{$menu->id}}" class="dropdown-menu border-0 shadow">
                                    @foreach ($menu->child as $child2)
                                        @if(isset($child2->child) && count($child2->child) > 0)
                                            <li class="dropdown-submenu dropdown-hover">
                                                <a id="dropdownSubMenu{{$child2->id}}" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle disabled">{{$child2->title}}</a>
                                                <ul aria-labelledby="dropdownSubMenu{{$child2->id}}" class="dropdown-menu border-0 shadow">
                                                    @foreach ($child2->child as $child3)
                                                        <li><a class="dropdown-item" href="{{url($child3->route_url)}}">{{$child3->title}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li class="nav-item"><a class="dropdown-item" href="{{url($child2->route_url)}}">{{$child2->title}}</a></li>
                                    @endif
                                @endforeach
                                <!-- End Level two -->
                                </ul>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{url($menu->route_url)}}">{{$menu->title}}</a></li>
                        @endif
                    @endforeach
                </ul>

                <!-- SEARCH FORM -->
                <form class="form-inline ml-0 ml-md-3">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-comments"></i>
                        <span class="badge badge-danger navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Brad Diesel
                                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">Call me whenever you can...</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="" alt="User Avatar" class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        John Pierce
                                        <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">I got your message bro</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="" alt="User Avatar" class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Nora Silvester
                                        <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">The subject goes here</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                    </div>
                </li>
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- /.navbar -->
    <!-- Content Wrapper. Contains page content -->
    <div id="pjax-container" class="content-wrapper  text-sm">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer text-sm">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            Anything you want
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2014-2020 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    </footer>
</div>
<!-- /主体 -->

<!-- 底部 -->
<!-- jQuery 3 -->
<script type="text/javascript" src="{{mix_build_dist('js/manifest.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/vendor.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/app.js')}}"></script>
{{--<script type="text/javascript" src="{{mix_build_dist('js/vue.js')}}"></script>--}}
{{--<script src="{{asset ('admin-ui/js/main.js?v='.get_version())}}"></script>--}}
{{--<!-- Bootstrap 3.3.7 -->--}}
{{--<script src="{{asset ('admin-ui/lib/bootstrap/dist/js/bootstrap.min.js')}}"></script>--}}
{{--<!-- AdminLTE App -->--}}
{{--<script src="{{asset ('admin-ui/dist/js/adminlte.min.js')}}"></script>--}}
{{--<!--弹窗-->--}}
{{----}}
<script src="{{asset ('admin-ui/lib/datejs/WdatePicker.js')}}"></script>
{{--<script src="{{asset ('admin-ui/lib/iColor/iColor-min.js')}}"></script>--}}
{{--<script src="{{asset ('admin-ui/lib/webuploader/webuploader.min.js')}}"></script>--}}
{{--<script src="{{asset ('js/units.js?v='.get_version ())}}"></script>--}}
{{--<script src="{{asset ('admin-ui/lib/address-picker/dist/js/address-picker.js')}}"></script>--}}

{{--<script type="text/javascript" charset="utf-8" src="{{asset ('admin-ui/lib/umeditor/umeditor.config.js')}}"></script>--}}
{{--<script type="text/javascript" charset="utf-8" src="{{asset ('admin-ui/lib/umeditor/umeditor.min.js')}}"></script>--}}
{{--<script type="text/javascript" src="{{asset ('admin-ui/lib/umeditor/lang/zh-cn/zh-cn.js')}}"></script>--}}
{{--<script type="text/javascript" src="{{asset ('admin-ui/lib/zTree/v3/js/jquery.ztree.core-3.5.js')}}"></script>--}}
{{--<script type="text/javascript" src="{{asset ('admin-ui/lib/zTree/v3/js/jquery.ztree.excheck-3.5.js')}}"></script>--}}
{{--<script src="{{asset ('admin-ui/lib/seiyria-bootstrap-slider/bootstrap-slider.min.js')}}"></script>--}}
{{--<!--  地图统计图表  -->--}}
{{--<!--  地图统计图表  -->--}}
{{--<script src="{{asset('admin-ui/lib/echarts/echarts.min.js')}}"></script>--}}
{{--<script src="{{asset('admin-ui/lib/echarts/echarts-gl.min.js')}}"></script>--}}
{{--<script src="http://gallery.echartsjs.com/dep/echarts/map/js/china.js"></script>--}}
{{--<script src="{{asset ('admin-ui/js/main.js?v='.get_version())}}"></script>--}}
@yield('footer')
{{--<script type="text/javascript">--}}
{{--  $ (function () {--}}
{{--    $ ('[data-toggle="tooltip"]').tooltip ()--}}

{{--    //--}}
{{--    layer.photos ({--}}
{{--      photos: '.layer-photos-preview'--}}
{{--      , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）--}}
{{--    });--}}

{{--    $ (".treeview-menu li").click (function () {--}}
{{--      $ (".treeview-menu li").removeClass ('active')--}}
{{--      $ (this).addClass ('active')--}}
{{--    });--}}

{{--      $('input:not([autocomplete]),textarea:not([autocomplete]),select:not([autocomplete])').attr('autocomplete', 'off');--}}
{{--  })--}}
{{--</script>--}}

<!-- /底部 -->
<a id="jump_blank_url" href="" class="hidden" target="_blank">跳转</a>
</body>
</html>
