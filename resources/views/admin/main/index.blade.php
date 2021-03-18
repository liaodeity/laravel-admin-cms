@extends('layouts.app')
@section('body_class','layui-layout-body layuimini-all')
@section('content')
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header header">
            <div class="layui-logo">
            </div>
            <a>
                <div class="layuimini-tool"><i title="展开" class="fa fa-outdent" data-side-fold="1"></i></div>
            </a>

            <ul class="layui-nav layui-layout-left layui-header-menu layui-header-pc-menu mobile layui-hide-xs">
            </ul>
            <ul class="layui-nav layui-layout-left layui-header-menu mobile layui-hide-sm">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="fa fa-list-ul"></i> 选择模块</a>
                    <dl class="layui-nav-child layui-header-mini-menu">
                    </dl>
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item" lay-unselect>
                    <a href="{{url ('/')}}" target="_blank" title="浏览官网首页"><i class="iconfont  iconliulan"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-refresh="刷新"><i class="fa fa-refresh"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-clear="清理" class="layuimini-clear"><i class="fa fa-trash-o"></i></a>
                </li>
                <li class="layui-nav-item mobile layui-hide-xs" lay-unselect>
                    <a href="javascript:;" data-check-screen="full"><i class="fa fa-arrows-alt"></i></a>
                </li>
                <li class="layui-nav-item layuimini-setting">
                    <a href="javascript:;">{{\App\Models\User::showName ($user->id)}}</a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="javascript:;" data-iframe-tab="/admin/user/password" data-title="修改密码"
                               data-icon="fa fa-gears">修改密码</a>
                        </dd>
                        <dd>
                            <a href="javascript:;" class="login-out">退出登录</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item layuimini-select-bgcolor mobile layui-hide-xs" lay-unselect>
                    <a href="javascript:;" data-bgcolor="配色方案"><i class="fa fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>

        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll layui-left-menu">
            </div>
        </div>

        <div class="layui-body">
            <div class="layui-tab" lay-filter="layuiminiTab" id="top_tabs_box">
                <ul class="layui-tab-title" id="top_tabs">
                    <li class="layui-this" id="layuiminiHomeTabId" lay-id=""></li>
                </ul>
                <ul class="layui-nav closeBox">
                    <li class="layui-nav-item">
                        <a href="javascript:;"> <i class="fa fa-dot-circle-o"></i> 页面操作</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" data-page-close="other"><i class="fa fa-window-close"></i> 关闭其他</a></dd>
                            <dd><a href="javascript:;" data-page-close="all"><i class="fa fa-window-close-o"></i> 关闭全部</a></dd>
                        </dl>
                    </li>
                </ul>
                <div class="layui-tab-content clildFrame">
                    <div id="layuiminiHomeTabIframe" class="layui-tab-item layui-show">
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('footer')
    <script>
        function layer_module_tips(module) {
            var index = layer.open({
                title: '',
                type: 2,
                shade: 0.2,
                maxmin: false,
                shadeClose: false,
                area: ['920px', '670px'],
                content: '/admin/manuals?module=' + module,
            });
        }

        layui.use(['element', 'layer', 'layuimini'], function () {
            var $ = layui.jquery,
                element = layui.element,
                layer = layui.layer;

            layuimini.init('{{route ('admin.main.init')}}');
            //
            $('.login-out').on("click", function () {
                layer.msg('退出登录成功', {
                    icon: 1,
                    time: SUCCESS_TIME
                    , shade: 0.2
                }, function () {
                    window.location = '{{route ('admin.main.logout')}}';
                });
            });
        });
    </script>
@endsection
