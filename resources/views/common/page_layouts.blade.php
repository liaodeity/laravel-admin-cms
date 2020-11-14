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
      // $.ajaxSetup ({
      //   headers: {
      //     'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content')
      //   }
      // });
    </script>
    @yield('style')
</head>
<body class="hold-transition layout-navbar-fixed layout-top-nav @yield('body_class') ">
<!-- 主体 -->
@yield('content')
<!-- /主体 -->

<!-- 底部 -->
<!-- jQuery 3 -->
<script type="text/javascript" src="{{mix_build_dist('js/manifest.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/vendor.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/app.js')}}"></script>

{{--<!-- Bootstrap 3.3.7 -->--}}
{{--<script src="{{asset ('admin-ui/lib/bootstrap/dist/js/bootstrap.min.js')}}"></script>--}}
{{--<!-- AdminLTE App -->--}}
{{--<script src="{{asset ('admin-ui/dist/js/adminlte.min.js')}}"></script>--}}
{{--<!--弹窗-->--}}
{{----}}
{{--<script src="{{asset ('admin-ui/lib/datejs/WdatePicker.js')}}"></script>--}}
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
