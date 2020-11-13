<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#2196f3">
    <meta http-equiv="Content-Security-Policy" content="default-src * 'self' 'unsafe-inline' 'unsafe-eval' data: gap:">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('member-ui/packages/css/framework7.bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset ('member-ui/fonts/iconfont/iconfont.css')}}">
    <link rel="stylesheet" href="{{asset ('member-ui/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('member-ui/css/styles.css')}}">
    <link rel="stylesheet" href="{{asset ('member-ui/css/common.css')}}">

    @yield('style')
</head>
<body class=" @yield('body_class') ">
<!-- 主体 -->
@yield('content')
<!-- /主体 -->

<!-- 底部 -->
<script src="http://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script src="{{asset ('admin-ui/lib/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset ('member-ui/packages/js/framework7.bundle.min.js')}}"></script>
<script src="{{asset ('js/units.js?v='.get_version ())}}"></script>
<script src="{{asset ('member-ui/js/clipboard.min.js')}}"></script>
<script src="{{asset ('member-ui/js/routes.js')}}"></script>
<script src="{{asset ('member-ui/js/app.js')}}"></script>
<script src="{{asset ('member-ui/js/function.js?v='.get_version())}}"></script>

<script>
    var SYS_TOKEN = '{{csrf_token()}}'
    $(function () {
        $.ajaxSetup ({
            headers: {
                'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content')
            }
        });
    })

</script>
@yield('footer')
</body>
</html>
