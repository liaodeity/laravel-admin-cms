@extends('layouts.app')
@section('style')
    <style>
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden
        }

        body {
            background: #009688;
        }

        body:after {
            content: '';
            background-repeat: no-repeat;
            background-size: cover;
            -webkit-filter: blur(3px);
            -moz-filter: blur(3px);
            -o-filter: blur(3px);
            -ms-filter: blur(3px);
            filter: blur(3px);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }

        .layui-container {
            width: 100%;
            height: 100%;
            overflow: hidden
        }

        .admin-login-background {
            width: 340px;
            height: 300px;
            position: absolute;
            left: 50%;
            top: 40%;
            margin-left: -170px;
            margin-top: -100px;
        }

        .logo-title {
            text-align: center;
            letter-spacing: 2px;
            padding: 14px 0;
        }

        .logo-title h1 {
            color: #009688;
            font-size: 25px;
            font-weight: bold;
        }

        .login-form {
            background-color: #fff;
            border: 1px solid #fff;
            border-radius: 3px;
            padding: 14px 20px;
            box-shadow: 0 0 8px #eeeeee;
        }

        .login-form .layui-form-item {
            position: relative;
        }

        .login-form .layui-form-item label {
            position: absolute;
            left: 1px;
            top: 1px;
            width: 38px;
            line-height: 36px;
            text-align: center;
            color: #d2d2d2;
        }

        .login-form .layui-form-item input {
            padding-left: 36px;
        }

        .captcha {
            width: 55%;
            display: inline-block;
        }

        .captcha-img {
            display: inline-block;
            vertical-align: top;
            background: url("{{asset ('static/admin/images/loading-2.gif')}}") no-repeat center;
            min-width: 32px;
        }

        .captcha-img img {
            border: 1px solid #e6e6e6;
            height: 36px;
            width: 100%;
        }

        .wx_scan {
            text-align: center
        }
        .wx_scan .pic{
            background: url("{{asset ('static/admin/images/loading-2.gif')}}") no-repeat center;
            height: 164px;
        }
        .wx_scan .pic .suc{
            display: none;
            position: absolute;
            top: 0;
            left: 17%;
            width: 164px;
            height: 164px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.96);
            vertical-align: middle;
            /*width:64px;*/
        }
        .wx_scan  .pic i {
            color: #07C160;
            font-size: 50px;
            position: absolute;
            top: 25%;
            left: 40%;
        }
        .wx_scan .pic h2{
            position: absolute;
            top: 55%;
            left: 27%;
        }
        .wx_scan img {
            width: 164px;
        }

        #captchaPic {
            cursor: pointer;
        }
    </style>
@endsection
@section('content')

    <div class="layui-container">
        <div class="admin-login-background">
            <div class="layui-form login-form">
                <div class="layui-row">
                    <div class="layui-form-item logo-title">
                        <h1>{{config('zhihe.name')}}后台登录</h1>
                    </div>
{{--                    <div class="layui-col-sm6">--}}
{{--                        <div class="wx_scan">--}}
{{--                            <div class="pic">--}}
{{--                                <div class="suc">--}}
{{--                                    <i class="iconfont icondagou"></i>--}}
{{--                                    <h2>扫码成功</h2>--}}
{{--                                </div>--}}
{{--                                <img id="login_qrcode" src="" alt="" style="display:none">--}}
{{--                            </div>--}}
{{--                            <p>微信扫一扫，直接登录</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="layui-col-sm12">
                        <form class="layui-form" action="" onsubmit="return false;">
                            <div class="layui-form-item">
                                <label class="layui-icon layui-icon-username" for="username"></label>
                                <input type="text" name="username" lay-verify="required|account" placeholder="用户名" autocomplete="off"
                                       class="layui-input" maxlength="20"
                                       value="">
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-icon layui-icon-password" for="password"></label>
                                <input type="password" name="password" lay-verify="required|password" placeholder="密码" autocomplete="off"
                                       class="layui-input" maxlength="64"
                                       value="">
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-icon layui-icon-vercode" for="captcha"></label>
                                <input type="text" name="captcha" placeholder="图形验证码" autocomplete="off"
                                       class="layui-input verification captcha" value="" maxlength="4">
                                <div class="captcha-img">
                                    <img id="captchaPic" class="layui-hide" data-src="{{route ('admin.login.captcha')}}" src="{{route ('admin.login.captcha')}}"
                                         title="点击刷新验证码">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn layui-btn-fluid" lay-submit="" lay-filter="login">登 入</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script src="{{asset ('static/layuimini-'.get_admin_theme().'/lib/jq-module/jquery.particleground.min.js')}}" charset="utf-8"></script>
    <script>
        layui.use(['form'], function () {
            var form = layui.form,
                layer = layui.layer;
            // 登录过期的时候，跳出ifram框架
            if (top.location != self.location) top.location = self.location;

            // 粒子线条背景
            $(document).ready(function () {
                $('.layui-container').particleground({
                    dotColor: '#5cbdaa',
                    lineColor: '#5cbdaa'
                });
            });
            $(document).on('click', '#captchaPic', function () {
                src = $(this).data('src');
                src += '?t=' + Math.random()
                $(this).attr('src', src)
            });
            $("#captchaPic").click().removeClass('layui-hide');//页面打开刷新一次验证码
            // 进行登录操作
            form.on('submit(login)', function (data) {
                data = data.field;
                if (data.username == '') {
                    layer.msg('用户名不能为空');
                    return false;
                }
                if (data.password == '') {
                    layer.msg('密码不能为空');
                    return false;
                }
                if (data.captcha == '') {
                    layer.msg('验证码不能为空');
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: '{{route ('admin.login.check')}}',
                    data: data,
                    dataType: 'json',
                    beforeSend: function () {
                        $("#button[lay-filter='login']").removeClass('disabled').prop('disabled', false);
                        loading = layer.load(2)
                    },
                    complete: function () {
                        $("#button[lay-filter='login']").removeClass('disabled').prop('disabled', false);
                        layer.close(loading)
                    },
                    error: function () {
                        top.layer.msg(AJAX_ERROR_TIP, {
                            icon: 2,
                            time: FAIL_TIME,
                            shade: 0.3
                        });
                        $("#captchaPic").click();
                    },
                    success: function (data) {
                        if (data.code === 0) {
                            layer.msg(data.message, {icon: 6, time: SUCCESS_TIME, shade: 0.2});
                            setTimeout(function () {
                                window.location = '{{route ('admin')}}';
                            }, SUCCESS_TIME);
                        } else {
                            top.layer.msg(data.message, {
                                icon: 2,
                                time: FAIL_TIME,
                                shade: 0.3
                            });
                            if (data.refresh === true) {
                                $("#captchaPic").click();
                                $("input[name='captcha']").val('')
                            }
                        }

                    }
                })
                return false;
            });
        });
    </script>
@endsection
