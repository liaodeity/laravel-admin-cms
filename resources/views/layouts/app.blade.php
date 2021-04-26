<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{get_config_value ('site_title')}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="icon" href="/favicon.ico">
    <link href="{{asset ('static/umeditor/themes/default/css/umeditor.css')}}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset ('static/layuimini/lib/layui-v2.5.5/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset ('static/layuimini/css/layuimini.css')}}" media="all">
    <link rel="stylesheet" href="{{asset ('static/layuimini/lib/font-awesome-4.7.0/css/font-awesome.min.css')}}" media="all">
    <link rel="stylesheet" href="{{asset ('static/layuimini/css/public.css?v='.get_version ())}}" media="all">
    <link rel="stylesheet" href="{{asset ('static/admin/fonts/iconfont.css?v='.get_version ())}}">
    <link rel="stylesheet" href="{{asset ('static/admin/css/admin.min.css?v='.get_version ())}}" media="all">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style id="layuimini-bg-color">
    </style>
    <script type="text/javascript">
        MODULE_NAME = '{{$MODULE_NAME ?? ''}}';
        SUCCESS_TIME = 800;
        FAIL_TIME = 2000;
        TABLE_RESIZE_TIME = 3500;
        AJAX_ERROR_TIP = '访问失败';
    </script>
    <style>
        .layui-tab-item.layui-show {
          overflow: hidden;
        }
    </style>
    @yield('style')
</head>
<body class="@yield('body_class')">
@yield('content')
<script src="{{asset ('static/admin/js/lang.js?v='.get_version ())}}" charset="utf-8"></script>
<script src="{{asset ('static/layuimini/lib/jquery-3.4.1/jquery-3.4.1.min.js')}}" charset="utf-8"></script>
<script src="{{asset ('static/layuimini/lib/layui-v2.5.5/layui.js?v=1.0.4')}}" charset="utf-8"></script>
<script src="{{asset ('static/layuimini/js/lay-config.js?v=1.0.4')}}" charset="utf-8"></script>
<script src="{{asset ('static/layuimini/js/lay-module/echarts/echarts.js')}}"></script>
<script type="text/javascript" charset="utf-8" src="{{asset ('static/umeditor/umeditor.config.js')}}"></script>
<script type="text/javascript" charset="utf-8" src="{{asset ('static/umeditor/umeditor.min.js')}}"></script>
<script type="text/javascript" src="{{asset ('static/umeditor/lang/zh-cn/zh-cn.js')}}"></script>
<script src="{{asset ('static/admin/js/admin.min.js?v='.get_version ())}}" charset="utf-8"></script>
<script src="{{asset ('static/admin/js/area.region.min.js?v='.get_version ())}}" charset="utf-8"></script>
<script src="{{asset ('static/axios/axios.min.js')}}" charset="utf-8"></script>

<script>
layui.use(['element', 'layer'], function () {
    var $ = layui.jquery,
        element = layui.element,
        layer = layui.layer;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>
@yield('footer')
<!--基于 https://gitee.com/liaodeiy/laravel-admin-cms 后台系统开发.-->
</body>
</html>
