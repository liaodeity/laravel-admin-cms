<!DOCTYPE HTML>
<html>
<head>

    <meta charset="utf-8">
    <title> @if(request()->is('agent*')){{get_config_value('agent_sys_title','代理商管理平台')}}@else后台管理系统@endif
        - {{get_config_value('system_company_name','')}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{mix_build_dist('/css/admin/vendor.css')}}">
    <link rel="stylesheet" href="{{mix_build_dist('/css/admin/app.css')}}">
</head>
<body>
<div id="app"></div>
<!-- 底部 -->
<!-- jQuery 3 -->
<script type="text/javascript" src="{{mix_build_dist('js/manifest.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/vendor.js')}}"></script>
<script type="text/javascript" src="{{mix_build_dist('js/app.js')}}"></script>

</body>
</html>
