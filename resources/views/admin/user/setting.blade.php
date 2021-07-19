@extends('layouts.app')
@section('style')

@endsection

@section('content')
    <div class="layuimini-container">
        <div class="layuimini-main">
            <form class="layui-form" action="" lay-filter="example" onsubmit="return false;">
                <div class="layui-form layuimini-form">
                    {{csrf_field ()}}
                    <input type="hidden" name="id" value="{{$user->id ?? ''}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label required">用户账号</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" lay-verify="required" disabled lay-reqtext="管理账号不能为空"
                                   placeholder="请输入管理账号" value="{{$user->name}}" class="layui-input layui-disabled">
                            <tip>登录账号无法修改。</tip>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">邮箱</label>
                        <div class="layui-input-block">
                            <input type="email" name="User[email]" placeholder="请输入邮箱" value="" class="layui-input">
                        </div>
                    </div>
                    @include('admin.user.form_user_info_add')
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="create">立即提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'layarea', 'table', 'tableSelect'], function () {
            var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit
                , laydate = layui.laydate,
                layarea = layui.layarea;
            table = layui.layarea;
            form.render();
            //日期
            laydate.render({
                elem: '#date',
                trigger: 'click',
                range: true
            });

            //监听提交
            form.on('submit(create)', function (data) {
                $.ajax({
                    type: 'POST',
                    url: '{{url('admin/user/setting')}}',
                    data: data.field,
                    dataType: 'json',
                    beforeSend: function () {
                        $("#button[lay-filter='create']").removeClass('disabled').prop('disabled', false);
                        loading = layer.load(2)
                    },
                    complete: function () {
                        $("#button[lay-filter='create']").removeClass('disabled').prop('disabled', false);
                        layer.close(loading)
                    },
                    error: function () {
                        layer.msg(AJAX_ERROR_TIP, {
                            icon: 2,
                            time: FAIL_TIME,
                            shade: 0.3
                        });
                    },
                    success: function (data) {
                        if (data.code === 0) {
                            layer.msg(data.message, {icon: 6, time: SUCCESS_TIME, shade: 0.2});
                            setTimeout(function () {
                                location.reload();
                            }, SUCCESS_TIME)
                        } else {
                            layer.msg(data.message, {
                                icon: 2,
                                time: FAIL_TIME,
                                shade: 0.3
                            });
                        }

                    }
                })

                return false;
            });
        });
    </script>
@endsection
